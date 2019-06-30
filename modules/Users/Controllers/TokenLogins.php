<?php
/**
 * Authorize - A Controller for managing the User Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Modules\Users\Controllers;

use Nova\Http\Request;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\Validator;

use Shared\Support\ReCaptcha;

use Modules\Platform\Controllers\BaseController;
use Modules\Users\Models\LoginToken;
use Modules\Users\Notifications\AuthenticationToken as TokenNotification;
use Modules\Users\Models\User;

use Carbon\Carbon;

use InvalidArgumentException;


class TokenLogins extends BaseController
{
    /**
     * The currently used Layout.
     *
     * @var mixed
     */
    protected $layout = 'Default';


    protected function validator(Request $request)
    {
        $rules = array(
            'email'                => 'required|valid_email',
            'g-recaptcha-response' => 'required|min:1|recaptcha'
        );

        $messages = array(
                'valid_email' => __d('users', 'The :attribute field is not a valid email address.'),
                'recaptcha'   => __d('users', 'The reCaptcha verification failed.'),
        );

        $attributes = array(
            'email'                => __d('users', 'E-mail'),
            'g-recaptcha-response' => __d('users', 'ReCaptcha'),
        );

        // Create a Validator instance.
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        // Add the custom Validation Rule commands.
        $validator->addExtension('valid_email', function($attribute, $value, $parameters)
        {
            return User::where('activated', 1)->where('email', $value)->exists();
        });

        $validator->addExtension('recaptcha', function($attribute, $value, $parameters) use ($request)
        {
            return ReCaptcha::check($value, $request->ip());
        });

        return $validator;
    }

    /**
     * Display the token request view.
     *
     * @return \Nova\View\View
     */
    public function index()
    {
        return $this->createView()
            ->shares('title', __d('users', 'One-Time Login'))
            ->shares('guard', 'web');
    }

    /**
     * Handle a POST request to token request.
     *
     * @return \Nova\Http\RedirectResponse
     */
    public function process(Request $request)
    {
        $remoteIp = $request->ip();

        // Get the limiter constraints.
        $maxAttempts = Config::get('platform::throttle.maxAttempts', 5);
        $lockoutTime = Config::get('platform::throttle.lockoutTime', 1); // In minutes.

        // Compute the throttle key.
        $throttleKey = 'users.tokenProcess|' .$remoteIp;

        // Make a Rate Limiter instance, via Container.
        $limiter = App::make('Nova\Cache\RateLimiter');

        if ($limiter->tooManyAttempts($throttleKey, $maxAttempts, $lockoutTime)) {
            $seconds = $limiter->availableIn($throttleKey);

            return Redirect::to('authorize')
                ->with('danger', __d('users', 'Too many attempts, please try again in {0} seconds.', $seconds));
        }

        // Create a Validator instance.
        $validator = $this->validator($request);

        if ($validator->fails()) {
            $limiter->hit($throttleKey, $lockoutTime);

            return Redirect::back()->withErrors($validator->errors());
        }

        $limiter->clear($throttleKey);

        $loginToken = LoginToken::create(array(
            'email' => $email = $request->input('email'),

            // We will use an unique token.
            'token' => $token = LoginToken::uniqueToken(),
        ));

        $hashKey = Config::get('app.key');

        $timestamp = dechex(time());

        $hash = hash_hmac('sha256', $token .'|' .$remoteIp .'|' .$timestamp, $hashKey);

        //
        $user = User::where('email', $email)->first();

        $user->notify(new TokenNotification($hash, $timestamp, $token));

        return Redirect::back()
            ->with('success', __d('users', 'Login instructions have been sent to your email address.'));
    }

    /**
     * Handle a login on token request.
     *
     * @return \Nova\Http\RedirectResponse
     */
    public function login(Request $request, $hash, $timestamp, $token)
    {
        $remoteIp = $request->ip();

        // Get the limiter constraints.
        $maxAttempts = Config::get('platform::throttle.maxAttempts', 5);
        $lockoutTime = Config::get('platform::throttle.lockoutTime', 1); // In minutes.

        // Compute the throttle key.
        $throttleKey = 'users.tokenLogin|' .$remoteIp;

        // Make a Rate Limiter instance, via Container.
        $limiter = App::make('Nova\Cache\RateLimiter');

        if ($limiter->tooManyAttempts($throttleKey, $maxAttempts, $lockoutTime)) {
            $seconds = $limiter->availableIn($throttleKey);

            return Redirect::to('authorize')
                ->with('danger', __d('users', 'Too many attempts, please try again in {0} seconds.', $seconds));
        }

        $validity = Config::get('platform::tokens.login.validity', 15); // In minutes.

        $oldest = Carbon::parse('-' .$validity .' minutes');

        //
        $hashKey = Config::get('app.key');

        $data = $token .'|' .$remoteIp .'|' .$timestamp;

        try {
            if (! hash_equals($hash, hash_hmac('sha256', $data, $hashKey)) || ($oldest->timestamp > hexdec($timestamp))) {
                throw new InvalidArgumentException('Invalid authorization atempt');
            }

            $loginToken = LoginToken::with('user')->whereHas('user', function ($query)
            {
                $query->where('activated', 1);

            })->where('token', $token)->where('created_at', '>', $oldest)->firstOrFail();
        }

        // Catch the exceptions.
        catch (InvalidArgumentException | ModelNotFoundException $e) {
            $limiter->hit($throttleKey, $lockoutTime);

            return Redirect::to('authorize')
                ->with('danger', __d('users', 'Link is invalid, please request a new link.'));
        }

        $limiter->clear($throttleKey);

        // Delete all stored login Tokens for this User.
        LoginToken::where('email', $loginToken->email)->delete();

        //
        $user = $loginToken->user;

        if ($user->activated == 0) {
            return Redirect::to('register/verify')
                ->withInput(array('email' => $user->email))
                ->with('danger', __d('users', 'Please activate your Account!'));
        }

        // Authenticate the User instance from login Token.
        Auth::login($user, false /* do not remember this login */);

        return Redirect::to('dashboard')
            ->with('success', __d('users', '<b>{0}</b>, you have successfully logged in.', $user->username));
    }
}
