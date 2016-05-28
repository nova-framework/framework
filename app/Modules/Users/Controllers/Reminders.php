<?php
/**
 * Reminders - A Controller for managing the Password Reminders.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Users\Controllers;

use Core\Controller;
use Core\View;
use Helpers\Csrf;
use Helpers\Password as Hash;
use Helpers\Url;

use Input;
use Redirect;
use Session;


class Reminders extends Controller
{
    protected $layout = 'custom';


    public function __construct()
    {
        parent::__construct();
    }

    protected function before()
    {
        View::share('currentUri', Url::detectUri());

        return parent::before();
    }

    /**
     * Display the password reminder view.
     *
     * @return Response
     */
    public function remind()
    {
        $error = Session::remove('error', array());

        return View::make('Reminders/Remind', 'Users')
            ->shares('title', __d('users', 'Password Reminder'))
            ->with('csrfToken', Csrf::makeToken())
            ->with('error', $error);
    }

    /**
     * Handle a POST request to remind a user of their password.
     *
     * @return Response
     */
    public function postRemind()
    {
        $credentials = Input::only('email');

        $error = array();

        switch ($response = Password::remind($credentials)) {
            case Password::INVALID_USER:
                return Redirect::to('remind')->with('error', $error[] = __('users', 'We can\'t find a User with that e-mail address.'));

            case Password::REMINDER_SENT:
                return Redirect::to('remind')->with('status', __('users', 'Password reminder sent!'));
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

        return View::make('Reminders/Reset', 'Users')
            ->shares('title', __d('users', 'Password Reset'))
            ->with('csrfToken', Csrf::makeToken())
            ->with('error', $error)
            ->with('token', $token);
    }

    /**
     * Handle a POST request to reset a user's password.
     *
     * @return Response
     */
    public function postReset()
    {
        $credentials = Input::only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = Password::reset($credentials, function($user, $password)
        {
            $user->password = Hash::make($password);

            $user->save();
        });

        $error = array();

        switch ($response) {
            case Password::INVALID_PASSWORD:
                $error[] = __('users', 'Passwords must be at least six characters and match the confirmation.');

                break;
            case Password::INVALID_TOKEN:
                $error[] = __('users', 'This password reset token is invalid.');

                break;
            case Password::INVALID_USER:
                $error[] = __('users', 'We can\'t find a User with that e-mail address.');

                break;
            case Password::PASSWORD_RESET:
                return Redirect::to('login');
        }

        return Redirect::to('password/reset')->with('error', $error);
    }

}
