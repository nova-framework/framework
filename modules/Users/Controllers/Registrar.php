<?php
/**
 * Authorize - A Controller for managing the User Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Modules\Users\Controllers;

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
use Carbon\Carbon;

use Shared\Support\ReCaptcha;

use Modules\Users\Models\VerifyToken;
use Modules\Users\Notifications\AccountActivation as AccountActivationNotification;
use Modules\Roles\Models\Role;
use Modules\Users\Models\User;

use Modules\Platform\Controllers\BaseController;

use InvalidArgumentException;


class Registrar extends BaseController
{
    protected $layout = 'Default';


    protected function validator(Request $request)
    {
        // Validation rules.
        $rules = array(
            'email'                => 'required|email|unique:users',
            'password'             => 'required|confirmed|strong_password',
            'g-recaptcha-response' => 'required|min:1|recaptcha'
        );

        $messages = array(
            'recaptcha'       => __d('users', 'The reCaptcha verification failed.'),
            'valid_name'      => __d('users', 'The :attribute field is not a valid name.'),
            'strong_password' => __d('users', 'The :attribute field is not strong enough.'),
        );

        $attributes = array(
            'username'             => __d('users', 'Username'),
            'email'                => __d('users', 'E-mail'),
            'password'             => __d('users', 'Password'),
            'g-recaptcha-response' => __d('users', 'ReCaptcha'),
        );

        // Create a Validator instance.
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        // Add the custom Validation Rule commands.
        $validator->addExtension('recaptcha', function($attribute, $value, $parameters) use ($request)
        {
            return ReCaptcha::check($value, $request->ip());
        });

        $validator->addExtension('strong_password', function($attribute, $value, $parameters)
        {
            $pattern = "/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/";

            return (preg_match($pattern, $value) === 1);
        });

        return $validator;
    }

    /**
     * Display the register view.
     *
     * @return \Nova\View\View
     */
    public function create()
    {
        return $this->createView()
            ->shares('title', __d('users', 'User Registration'));
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
        $remoteIp = $request->ip();

        // Create a Validator instance.
        $validator = $this->validator($request);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator->errors());
        }

        $email    = $request->input('email');
        $password = $request->input('password');

        // Create the User record.
        $user = User::create(array(
            'username'  => $request->input('username'),
            'email'     => $email,
            'password'  => Hash::make($password),
            'activated' => 0,
        ));

        // Retrieve the default 'user' Role.
        $role = Role::where('slug', 'user')->firstOrFail();

        // Update the user's associated Roles.
        $user->roles()->attach($role);

        // Create a new Verification Token instance.
        $verifyToken = VerifyToken::create(array(
            'email' => $email,

            // We will use an unique token.
            'token' => $token = VerifyToken::uniqueToken(),
        ));

        // Send the associated Activation Notification.
        $hashKey = Config::get('app.key');

        $timestamp = dechex(time());

        $hash = hash_hmac('sha256', $token .'|' .$remoteIp .'|' .$timestamp, $hashKey);

        $user->notify(new AccountActivationNotification($hash, $timestamp, $token));

        return Redirect::to('register/status')
            ->with('success', __d('users', 'Your Account has been created. Activation instructions have been sent to your email address.'));
    }

    /**
     * Display the email verification view.
     *
     * @return \Nova\View\View
     */
    public function verify()
    {
        return $this->createView()
            ->shares('title', __d('users', 'Account Verification'));
    }

    /**
     * Process the verification token.
     *
     * @return \Nova\Http\RedirectResponse
     */
    public function verifyPost(Request $request)
    {
        $remoteIp = $request->ip();

        // Create a Validator instance.
        $validator = Validator::make(
            $request->only('email', 'g-recaptcha-response'),
            array(
                'email'                => 'required|valid_email',
                'g-recaptcha-response' => 'required|min:1|recaptcha'
            ),
            array(
                'valid_email' => __d('users', 'The :attribute field is not a valid email address.'),
                'recaptcha'   => __d('users', 'The reCaptcha verification failed.'),
            ),
            array(
                'email'                => __d('users', 'E-mail'),
                'g-recaptcha-response' => __d('users', 'ReCaptcha'),
            )
        );

        // Add the custom Validation Rule commands.
        $validator->addExtension('valid_email', function($attribute, $value, $parameters)
        {
            return User::where('activated', 0)->where('email', $value)->exists();
        });

        $validator->addExtension('recaptcha', function($attribute, $value, $parameters) use ($request)
        {
            return ReCaptcha::check($value, $request->ip());
        });

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator->errors());
        }

        // Create a new Verification Token instance.
        $verifyToken = VerifyToken::create(array(
            'email' => $email = $request->input('email'),

            // We will use an unique token.
            'token' => $token = VerifyToken::uniqueToken(),
        ));

        // Send the associated Activation Notification.
        $hashKey = Config::get('app.key');

        $timestamp = dechex(time());

        $hash = hash_hmac('sha256', $token .'|' .$remoteIp .'|' .$timestamp, $hashKey);

        //
        $user = User::where('email', $email)->first();

        $user->notify(new AccountActivationNotification($hash, $timestamp, $token));

        return Redirect::to('register/status')
            ->with('success', __d('users', 'Activation instructions have been sent to your email address.'));
    }

    /**
     * Process the verification token.
     *
     * @return \Nova\Http\RedirectResponse
     */
    public function tokenVerify(Request $request, $hash, $timestamp, $token)
    {
        $remoteIp = $request->ip();

        // Get the limiter constraints.
        $maxAttempts = Config::get('users::throttle.maxAttempts', 5);
        $lockoutTime = Config::get('users::throttle.lockoutTime', 1); // In minutes.

        // Compute the throttle key.
        $throttleKey = 'registrar.verify|' .$remoteIp;

        // Make a Rate Limiter instance, via Container.
        $limiter = App::make('Nova\Cache\RateLimiter');

       if ($limiter->tooManyAttempts($throttleKey, $maxAttempts, $lockoutTime)) {
            $seconds = $limiter->availableIn($throttleKey);

            return Redirect::to('register/status')
                ->with('danger', __d('users', 'Too many verification attempts, please try again in {0} seconds.', $seconds));
        }

        $validity = Config::get('users::tokens.activation.validity', 60); // In minutes.

        $oldest = Carbon::parse('-' .$validity .' minutes');

        //
        $hashKey = Config::get('app.key');

        $data = $token .'|' .$remoteIp .'|' .$timestamp;

        try {
            if (! hash_equals($hash, hash_hmac('sha256', $data, $hashKey)) || ($oldest->timestamp > hexdec($timestamp))) {
                throw new InvalidArgumentException('Invalid authorization atempt');
            }

            $verifyToken = VerifyToken::whereHas('user', function ($query)
            {
                $query->where('activated', 0);

            })->where('token', $token)->where('created_at', '>', $oldest)->firstOrFail();
        }

        // Catch the ORM exceptions.
        catch (InvalidArgumentException | ModelNotFoundException $e) {
            $limiter->hit($throttleKey, $lockoutTime);

            return Redirect::to('password/verify')
                ->with('danger', __d('users', 'Link is invalid, please request a new link.'));
        }

        // Delete all stored verification Tokens for this User.
        VerifyToken::where('email', $verifyToken->email)->delete();

        // Get a fresh instance of the associated User model.
        $user = $verifyToken->user()->first();

        // Update the User information.
        $user->activated = 1;

        $user->save();

        // Redirect to the login page.
        $guard = Config::get('auth.defaults.guard', 'web');

        $uri = Config::get("auth.guards.{$guard}.authorize", 'login');

        return Redirect::to($uri)
            ->with('success', __d('users', 'Your Account was activated. You can now sign in!'));
    }

    /**
     * Display the registration status.
     *
     * @return \Nova\View\View
     */
    public function status()
    {
        return $this->createView()
            ->shares('title', __d('users', 'Registration Status'));
    }
}
