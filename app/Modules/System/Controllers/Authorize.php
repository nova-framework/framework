<?php
/**
 * Authorize - A Controller for managing the User Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\System\Controllers;

use Nova\Helpers\ReCaptcha;

use App\Core\BackendController;

use App;
use Auth;
use Hash;
use Input;
use Password;
use Redirect;
use Response;
use Session;
use View;


class Authorize extends BackendController
{
    protected $layout = 'Default';


    /**
     * Display the login view.
     *
     * @return Response
     */
    public function login()
    {
        return $this->getView()
            ->shares('title', __d('system', 'User Login'));
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
            $status = __d('system', 'Invalid reCAPTCHA submitted.');

            return Redirect::back()->withStatus($status, 'danger');
        }

        // Retrieve the Authentication credentials.
        $credentials = Input::only('username', 'password');

        // Prepare the 'remember' parameter.
        $remember = (Input::get('remember') == 'on');

        // Make an attempt to login the Guest with the given credentials.
        if(! Auth::attempt($credentials, $remember)) {
            // An error has happened on authentication.
            $status = __d('system', 'Wrong username or password.');

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

        if($user->active == 0) {
            Auth::logout();

            // User not activated; go logout and redirect him back.
            $status = __d('system', 'There is a problem. Have you activated your Account?');

            return Redirect::back()->withStatus($status, 'warning');
        }

        // Prepare the flash message.
        $status = __d('system', '<b>{0}</b>, you have successfully logged in.', $user->username);

        // Redirect to the User's Dashboard.
        return Redirect::intended('admin/dashboard')->withStatus($status);
    }

    /**
     * Handle a GET request to logout the current User.
     *
     * @return Response
     */
    public function logout()
    {
        Auth::logout();

        // Prepare the flash message.
        $status = __d('system', 'You have successfully logged out.');

        return Redirect::to('login')->withStatus($status);
    }

    /**
     * Display the password reminder view.
     *
     * @return Response
     */
    public function remind()
    {
        return $this->getView()
            ->shares('title', __d('system', 'Password Recovery'));
    }

    /**
     * Handle a POST request to remind a User of their password.
     *
     * @return Response
     */
    public function postRemind()
    {
        $error = array();

        // Verify the reCAPTCHA
        if(! ReCaptcha::check()) {
            $status = __d('system', 'Invalid reCAPTCHA submitted.');

            return Redirect::back()->withStatus($status, 'danger');
        }

        //
        $credentials = Input::only('email');

        switch ($response = Password::remind($credentials)) {
            case Password::INVALID_USER:
                $status = __d('system', 'We can\'t find a User with that e-mail address.');

                return Redirect::back()->withStatus($status, 'danger');

            case Password::REMINDER_SENT:
                $status = __d('system', 'Reset instructions have been sent to your email address');

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
        return $this->getView()
            ->shares('title', __d('system', 'Password Reset'))
            ->with('token', $token);
    }

    /**
     * Handle a POST request to reset a User's password.
     *
     * @return Response
     */
    public function postReset()
    {
        // Verify the reCAPTCHA
        if(! ReCaptcha::check()) {
            $status = __d('system', 'Invalid reCAPTCHA submitted.');

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
                $status = __d('system', 'Passwords must be strong enough and match the confirmation.');

                break;
            case Password::INVALID_TOKEN:
                $status = __d('system', 'This password reset token is invalid.');

                break;
            case Password::INVALID_USER:
                $status = __d('system', 'We can\'t find a User with that e-mail address.');

                break;
            case Password::PASSWORD_RESET:
                $status = __d('system', 'You have successfully reset your Password.');

                return Redirect::to('login')->withStatus($status);
        }

        return Redirect::back()->withStatus($status, 'danger');
    }

}
