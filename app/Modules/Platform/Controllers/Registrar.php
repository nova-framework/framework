<?php
/**
 * Authorize - A Controller for managing the User Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Platform\Controllers;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;
use Nova\Support\Str;

use Shared\Support\ReCaptcha;

use App\Modules\Platform\Notifications\AccountActivation as AccountActivationNotification;
use App\Modules\Roles\Models\Role;
use App\Modules\Users\Models\User;

use App\Modules\Platform\Controllers\BaseController;


class Registrar extends BaseController
{
    protected $layout = 'Default';


    protected function validator(array $data, $remoteIp)
    {
        // Validation rules.
        $rules = array(
            'email'                => 'required|email|unique:users',
            'password'             => 'required|confirmed|strong_password',
            'g-recaptcha-response' => 'required|min:1|recaptcha'
        );

        $messages = array(
            'recaptcha'       => __d('platform', 'The reCaptcha verification failed.'),
            'valid_name'      => __d('platform', 'The :attribute field is not a valid name.'),
            'strong_password' => __d('platform', 'The :attribute field is not strong enough.'),
        );

        $attributes = array(
            'username'             => __d('platform', 'Username'),
            'email'                => __d('platform', 'E-mail'),
            'password'             => __d('platform', 'Password'),
            'g-recaptcha-response' => __d('platform', 'ReCaptcha'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('recaptcha', function($attribute, $value, $parameters) use ($remoteIp)
        {
            return ReCaptcha::check($value, $remoteIp);
        });

        Validator::extend('strong_password', function($attribute, $value, $parameters)
        {
            $pattern = "/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/";

            return (preg_match($pattern, $value) === 1);
        });

        return Validator::make($data, $rules, $messages, $attributes);
    }

    /**
     * Display the register view.
     *
     * @return \Nova\View\View
     */
    public function create()
    {
        return $this->createView()
            ->shares('title', __d('platform', 'User Registration'));
    }

    /**
     * Handle a POST request to login the User.
     *
     * @return \Nova\Http\RedirectResponse
     *
     * @throws \RuntimeException
     */
    public function store(Request $request)
    {
        $input = $request->only(
            'username', 'email', 'password', 'password_confirmation', 'g-recaptcha-response'
        );

        // Create a Validator instance.
        $validator = $this->validator($input, $request->ip());

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withStatus($validator->errors(), 'danger');
        }

        // Encrypt the given Password.
        $password = Hash::make($input['password']);

        // Create the User record.
        $user = User::create(array(
            'username' => $input['username'],
            'email'    => $input['email'],
            'password' => $password,
        ));

        // Update the Meta / Custom Fields.
        $user->load('meta');

        // Handle the meta-data.
        $user->meta->activated = 0;

        $user->meta->activation_code = $token = $this->createNewToken();

        $user->save();

        // Retrieve the default 'user' Role.
        $role = Role::where('slug', 'user')->firstOrFail();

        // Update the user's associated Roles.
        $user->roles()->attach($role);

        // Send the associated Activation Notification.
        $hashKey = Config::get('app.key');

        $hash = hash_hmac('sha256', $token, $hashKey);

        $user->notify(new AccountActivationNotification($hash, $token));

        return Redirect::to('register/status')
            ->withStatus(__d('platform', 'Your Account has been created. Activation instructions have been sent to your email address.'), 'success');
    }

    /**
     * Display the email verification view.
     *
     * @return \Nova\View\View
     */
    public function verify()
    {
        return $this->createView()
            ->shares('title', __d('platform', 'Account Verification'));
    }

    /**
     * Process the verification token.
     *
     * @return \Nova\Http\RedirectResponse
     */
    public function verifyPost(Request $request)
    {
        $remoteIp = $request->ip();

        Validator::extend('recaptcha', function($attribute, $value, $parameters) use ($remoteIp)
        {
            return ReCaptcha::check($value, $remoteIp);
        });

        $validator = Validator::make(
            $input = $request->only('email', 'g-recaptcha-response'),
            array(
                'email'                => 'required|email|exists:users,email',
                'g-recaptcha-response' => 'required|min:1|recaptcha'
            ),
            array(
                'recaptcha' => __d('platform', 'The reCaptcha verification failed.'),
            ),
            array(
                'email'                => __d('platform', 'E-mail'),
                'g-recaptcha-response' => __d('platform', 'ReCaptcha'),
            )
        );

        if ($validator->fails()) {
            return Redirect::back()->withStatus($validator->errors(), 'danger');
        }

        $email = $input['email'];

        try {
            $user = User::where('email', $email)->whereMeta('activated', 0)->firstOrFail();
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()
                ->withInput(array('email' => $email))
                ->withStatus(__d('platform', 'The selected email cannot receive Account Activation links.', $email), 'danger');
        }

        $user->meta->activation_code = $token = $this->createNewToken();

        $user->save();

        // Send the associated Activation Notification.
        $hashKey = Config::get('app.key');

        $hash = hash_hmac('sha256', $token, $hashKey);

        $user->notify(new AccountActivationNotification($hash, $token));

        return Redirect::to('register/verify')
            ->withStatus(__d('platform', 'Activation instructions have been sent to your email address.'), 'success');
    }

    /**
     * Process the verification token.
     *
     * @return \Nova\Http\RedirectResponse
     */
    public function tokenVerify(Request $request, $hash, $token)
    {
        $remoteIp = $request->ip();

        // Get the limiter constraints.
        $maxAttempts = Config::get('platform::throttle.maxAttempts', 5);
        $lockoutTime = Config::get('platform::throttle.lockoutTime', 1); // In minutes.

        // Compute the throttle key.
        $throttleKey = 'registrar.verify|' .$remoteIp;

        // Make a Rate Limiter instance, via Container.
        $limiter = App::make('Nova\Cache\RateLimiter');

       if ($limiter->tooManyAttempts($throttleKey, $maxAttempts, $lockoutTime)) {
            $seconds = $limiter->availableIn($throttleKey);

            return Redirect::to('register/status')
                ->withStatus(__d('platform', 'Too many verification attempts, please try again in {0} seconds.', $seconds), 'danger');
        }

        $hashKey = Config::get('app.key');

        if (! hash_equals($hash, hash_hmac('sha256', $token, $hashKey))) {
            $limiter->hit($throttleKey, $lockoutTime);

            return Redirect::to('register/status')
                ->withStatus(__d('platform', 'Link is invalid, please request a new link.'), 'danger');
        }

        try {
            $user = User::whereHas('meta', function ($query) use ($token)
            {
                return $query->where(function ($query)
                {
                    return $query->where('key', 'activated')->where('value', 0);

                })->where(function ($query) use ($token)
                {
                    return $query->where('key', 'activation_code')->where('value', $token);
                });

            })->firstOrFail();
        }
        catch (ModelNotFoundException $e) {
            $limiter->hit($throttleKey, $lockoutTime);

            return Redirect::to('password/remind')
                ->withStatus(__d('platform', 'Link is invalid, please request a new link.'), 'danger');
        }

        $user->activated = 1;

        $user->activation_code = null;

        $user->save();

        // Redirect to the login page.
        $guard = Config::get('auth.defaults.guard', 'web');

        $uri = Config::get("auth.guards.{$guard}.authorize", 'login');

        return Redirect::to($uri)
            ->withStatus(__d('platform', 'Activated! You can now Sign in!'), 'success');
    }

    /**
     * Display the registration status.
     *
     * @return \Nova\View\View
     */
    public function status()
    {
        return $this->createView()
            ->shares('title', __d('platform', 'Registration Status'));
    }

    /**
     * Create a new unique Token for the User.
     *
     * @return string
     */
    public function createNewToken()
    {
        $tokens = User::whereNotNull('activation_code')->lists('activation_code');

        do {
            $token = Str::random(100);
        }
        while (in_array($token, $tokens));

        return $token;
    }
}
