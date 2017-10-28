<?php
/**
 * Authorize - A Controller for managing the User Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Platform\Controllers;

use Nova\Helpers\ReCaptcha;
use Nova\Http\Request;

use Nova\Support\Facades\App;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\Session;
use Nova\Support\Facades\Validator;

use Shared\Support\Facades\Password;

use App\Modules\Platform\Controllers\BaseController;
use App\Modules\Platform\Models\UserToken as LoginToken;
use App\Modules\Platform\Notifications\AuthenticationToken as LoginTokenNotification;

use Carbon\Carbon;


class Authorize extends BaseController
{
    protected $layout = 'Default';


    /**
     * Display the login view.
     *
     * @return Response
     */
    public function login()
    {
        return $this->createView()
            ->shares('title', __d('platform', 'User Login'));
    }

    /**
     * Handle a POST request to login the User.
     *
     * @return Response
     */
    public function postLogin()
    {
        // Verify the submitted reCAPTCHA
        if(! ReCaptcha::check()) {
            $status = __d('platform', 'Invalid reCAPTCHA submitted.');

            return Redirect::back()->withStatus($status, 'danger');
        }

        // Retrieve the Authentication credentials.
        $credentials = Input::only('username', 'password');

        // Prepare the 'remember' parameter.
        $remember = (Input::get('remember') == 'on');

        // Make an attempt to login the Guest with the given credentials.
        if(! Auth::attempt($credentials, $remember)) {
            // An error has happened on authentication.
            $status = __d('platform', 'Wrong username or password.');

            return Redirect::back()->withStatus($status, 'danger');
        }

        // The User is authenticated now; retrieve his Model instance.
        $user = Auth::user();

        if (Hash::needsRehash($user->password)) {
            $password = $credentials['password'];

            $user->password = Hash::make($password);

            // Save the User Model instance - used with the Extended Auth Driver.
            $user->save();
        }

        if($user->activated == 0) {
            Auth::logout();

            // User not activated; go logout and redirect him back.
            $status = __d('platform', 'There is a problem. Have you activated your Account?');

            return Redirect::back()->withStatus($status, 'warning');
        }

        // Prepare the flash message.
        $status = __d('platform', '<b>{0}</b>, you have successfully logged in.', $user->username);

        // Redirect to the User's Dashboard.
        return Redirect::intended('dashboard')->withStatus($status);
    }

    /**
     * Handle a GET request to logout the current User.
     *
     * @return \Nova\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::logout();

        // Prepare the flash message.
        $status = __d('platform', 'You have successfully logged out.');

        return Redirect::to('login')->withStatus($status);
    }

    /**
     * Display the password reminder view.
     *
     * @return Response
     */
    public function remind()
    {
        return $this->createView()
            ->shares('title', __d('platform', 'Password Recovery'));
    }

    /**
     * Handle a POST request to remind a User of their password.
     *
     * @return \Nova\Http\RedirectResponse
     */
    public function postRemind()
    {

        // Verify the reCAPTCHA
        if(! ReCaptcha::check()) {
            $status = __d('platform', 'Invalid reCAPTCHA submitted.');

            return Redirect::back()->withStatus($status, 'danger');
        }

        //
        $credentials = Input::only('email');

        switch ($response = Password::remind($credentials)) {
            case Password::INVALID_USER:
                $status = __d('platform', 'We can\'t find a User with that e-mail address.');

                return Redirect::back()->withStatus($status, 'danger');

            case Password::REMINDER_SENT:
                $status = __d('platform', 'Reset instructions have been sent to your email address');

                return Redirect::back()->withStatus($status);
        }
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string  $token
     * @return Response
     */
    public function reset($token)
    {
        return $this->createView()
            ->shares('title', __d('platform', 'Password Reset'))
            ->with('token', $token);
    }

    /**
     * Handle a POST request to reset a User's password.
     *
     * @return \Nova\Http\RedirectResponse
     */
    public function postReset()
    {
        // Verify the reCAPTCHA
        if(! ReCaptcha::check()) {
            $status = __d('platform', 'Invalid reCAPTCHA submitted.');

            return Redirect::back()->withStatus($status, 'danger');
        }

        $credentials = Input::only(
            'email', 'password', 'password_confirmation', 'token'
        );

        // Add to Password Broker a custom validation.
        Password::validator(function($credentials)
        {
            $pattern = "/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/";

            return (preg_match($pattern, $credentials['password']) === 1);
        });

        $response = Password::reset($credentials, function($user, $password)
        {
            $user->password = Hash::make($password);

            $user->save();
        });

        // Parse the response.
        switch ($response) {
            case Password::INVALID_PASSWORD:
                $status = __d('platform', 'Passwords must be strong enough and match the confirmation.');

                break;
            case Password::INVALID_TOKEN:
                $status = __d('platform', 'This password reset token is invalid.');

                break;
            case Password::INVALID_USER:
                $status = __d('platform', 'We can\'t find a User with that e-mail address.');

                break;
            case Password::PASSWORD_RESET:
                $status = __d('platform', 'You have successfully reset your Password.');

                return Redirect::to('login')->withStatus($status);
        }

        return Redirect::back()->withStatus($status, 'danger');
    }

    public function tokenRequest()
    {
        return $this->createView()
            ->shares('title', __d('platform', 'One-Time Login'))
            ->shares('guard', 'web');
    }

    public function tokenProcess(Request $request)
    {
        Validator::extend('recaptcha', function($attribute, $value, $parameters) use ($request)
        {
            return ReCaptcha::check($value, $request->ip());
        });

        $validator = Validator::make(
            $input = $request->only('email', 'g-recaptcha-response'),
            array(
                'email'                => 'required|email|exists:users',
                'g-recaptcha-response' => 'required|recaptcha'
            ),
            array(
                'recaptcha' => __d('platform', 'The reCaptcha verification failed. Try again.'),
            )
        );

        if ($validator->fails()) {
            return Redirect::back()->withStatus($validator->errors(), 'danger');
        }

        $token = LoginToken::uniqueToken();

        $loginToken = LoginToken::create(array(
            'email' => $input['email'],
            'token' => $token,
        ));

        $loginToken->user->notify(new LoginTokenNotification($token));

        return Redirect::back()
            ->withStatus(__d('platform', 'Login instructions have been sent to the Center email address.'), 'success');
    }

    public function tokenLogin(Request $request, $token)
    {
        $maxAttempts = Config::get('centers::tokenLogin.maxAttempts', 5);
        $lockoutTime = Config::get('centers::tokenLogin.lockoutTime', 1); // In minutes.

        $validity = Config::get('centers::tokenLogin.validity', 15); // In minutes.

        // Make a Rate Limiter instance, via Container.
        $limiter = App::make('Nova\Cache\RateLimiter');

        // Compute the throttle key.
        $throttleKey = 'users.tokenLogin|' .$request->ip();

        if ($limiter->tooManyAttempts($throttleKey, $maxAttempts, $lockoutTime)) {
            $seconds = $limiter->availableIn($throttleKey);

            return Redirect::to('authorize')
                ->withStatus(__d('platform', 'Too many login attempts, please try again in {0} seconds.', $seconds), 'danger');
        }

        try {
            $loginToken = LoginToken::with('user')
                ->where('token', $token)
                ->where('created_at', '>', Carbon::parse('-' .$validity .' minutes'))
                ->firstOrFail();
        }
        catch (ModelNotFoundException $e) {
            $limiter->hit($throttleKey, $lockoutTime);

            return Redirect::to('authorize')
                ->withStatus(__d('platform', 'Link is invalid, please request a new link.'), 'danger');
        }

        $limiter->clear($throttleKey);

        // Delete all stored login Tokens for this Center.
        LoginToken::where('email', $loginToken->email)->delete();

        // Authenticate the Center instance.
        Auth::login($loginToken->user, true /* remember */);

        return Redirect::to('dashboard')
            ->withStatus(__d('platform', '<b>{0}</b>, you have successfully logged in.', $loginToken->user->username), 'success');
    }
}
