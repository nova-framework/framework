<?php
/**
 * Authorize - A Controller for managing the User Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Users\Controllers;

use Core\Controller;
use Core\View;
use Helpers\Url;
use Helpers\ReCaptcha;

use Auth;
use Hash;
use Input;
use Password;
use Redirect;
use Response;
use Session;


class Authorize extends Controller
{
    protected $template = 'AdminLte';
    protected $layout   = 'default';


    public function __construct()
    {
        parent::__construct();

        // Prepare the Users Model instance - while using the Database Auth Driver.
        //$this->model = new \App\Modules\Users\Models\Users();
    }

    protected function before()
    {
        View::share('currentUri', Url::detectUri());

        return parent::before();
    }

    /**
     * Display the login view.
     *
     * @return Response
     */
    public function login()
    {
        $error = Session::remove('error', array());

        //View::share('js', 'https://www.google.com/recaptcha/api.js');

        return $this->getView()
            ->shares('title', __d('users', 'User Login'))
            ->with('csrfToken', Session::token())
            ->with('error', $error);
    }

    /**
     * Handle a POST request to login the User.
     *
     * @return Response
     */
    public function postLogin()
    {
        $error = array();

        // Verify the submitted reCAPTCHA
        if(! ReCaptcha::check()) {
            return Redirect::back()->with('error', $error[] = __d('users', 'Invalid reCAPTCHA submitted.'));
        }

        // Retrieve the Authentication credentials.
        $credentials = Input::only('username', 'password');

        // Prepare the 'remember' parameter.
        $remember = (Input::get('remember') == 'on');

        // Make an attempt to login the Guest with the given credentials.
        if(Auth::attempt($credentials, $remember)) {
            // The User is authenticated now; retrieve his Model instance.
            $user = Auth::user();

            if (Hash::needsRehash($user->password)) {
                $password = $credentials['password'];

                $user->password = Hash::make($password);

                // Save the User Model instance - used with the Extended Auth Driver.
                $user->save();

                // Save the User Model instance - used with the Database Auth Driver.
                //$this->model->updateGenericUser($user);
            }

            // Prepare the flash message.
            $message = __d('users', '<b>{0}</b>, you have successfully logged in.', $user->username);

            // Redirect to the User's Dashboard.
            return Redirect::to('users/dashboard')->with('message', $message);
        }

        // An error has happened on authentication; add a message into $error array.
        $error[] = __d('users', 'Wrong username or password.');

        return Redirect::back()->with('error', $error);
    }

    /**
     * Handle a GET request to logout the current User.
     *
     * @return Response
     */
    public function logout()
    {
        Auth::logout();

        return Redirect::to('login')->with('message', __d('users', 'You have successfully logged out.'));
    }

    /**
     * Display the password reminder view.
     *
     * @return Response
     */
    public function remind()
    {
        $error = Session::remove('error', array());

        return $this->getView()
            ->shares('title', __d('users', 'Password Recovery'))
            ->with('csrfToken', Session::token())
            ->with('error', $error);
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
            return Redirect::back()->with('error', $error[] = __d('users', 'Invalid reCAPTCHA submitted.'));
        }

        //
        $credentials = Input::only('email');

        switch ($response = Password::remind($credentials)) {
            case Password::INVALID_USER:
                return Redirect::back()->with('error', $error[] = __d('users', 'We can\'t find a User with that e-mail address.'));

            case Password::REMINDER_SENT:
                return Redirect::back()->with('message', __d('users', 'Reset instructions have been sent to your email address'));
        }
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string  $token
     * @return Response
     */
    public function reset($token = null)
    {
        if (is_null($token)) return Response::error(404);

        $error = Session::remove('error', array());

        //View::share('js', 'https://www.google.com/recaptcha/api.js');

        return $this->getView()
            ->shares('title', __d('users', 'Password Reset'))
            ->with('csrfToken', Session::token())
            ->with('error', $error)
            ->with('token', $token);
    }

    /**
     * Handle a POST request to reset a User's password.
     *
     * @return Response
     */
    public function postReset()
    {
        $error = array();

        // Verify the reCAPTCHA
        if(! ReCaptcha::check()) {
            return Redirect::back()->with('error', $error[] = __d('users', 'Invalid reCAPTCHA submitted.'));
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
                $error[] = __d('users', 'Passwords must be strong enough and match the confirmation.');

                break;
            case Password::INVALID_TOKEN:
                $error[] = __d('users', 'This password reset token is invalid.');

                break;
            case Password::INVALID_USER:
                $error[] = __d('users', 'We can\'t find a User with that e-mail address.');

                break;
            case Password::PASSWORD_RESET:
                return Redirect::to('login')->with('message', __d('users', 'You have successfully reset your Password.'));
        }

        return Redirect::back()->with('error', $error);
    }

}
