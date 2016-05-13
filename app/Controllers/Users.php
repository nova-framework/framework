<?php
/**
 * Users - A Controller for managing the Users Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Controllers;

use Core\Controller;
use Core\Redirect;
use Core\View;
use Helpers\Csrf;
use Helpers\Request;
use Helpers\Password;
use Helpers\Url;
use Auth;


class Users extends Controller
{
    protected $layout = 'custom';

    protected $model;


    public function __construct()
    {
        parent::__construct();

        // Prepare the Users Model instance.
        $this->model = new \App\Models\Users();
    }

    protected function before()
    {
        View::share('currentUri', Url::detectUri());

        return parent::before();
    }

    public function dashboard()
    {
        return View::make('Users/Dashboard')->shares('title', 'Dashboard');
    }

    public function login()
    {
        $error = array();

        if(Request::isPost()) {
            // Prepare the Authentication credentials.
            $credentials = array(
                'username' => Request::post('username'),
                'password' => Request::post('password')
            );

            // Prepare the 'remember' parameter.
            $remember = (Request::post('remember') == 'on');

            // Make an attempt to login the Guest with the given credentials.
            if(Auth::attempt($credentials, $remember)) {
                // The User is authenticated now; retrieve his data as an stdClass instance.
                $user = Auth::user();

                // Prepare the flash message.
                $message = sprintf('<b>%s</b>, you have successfully logged in.', $user->realname);

                // Redirect to the User's Dashboard.
                return Redirect::to('dashboard')->with('message', $message);
            } else {
                // An error has happened on authentication; add a message into $error array.
                $error[] = 'Wrong username or password.';
            }
        }

        return View::make('Users/Login')
            ->shares('title', 'User Login')
            ->with('csrfToken', Csrf::makeToken())
            ->with('error', $error);
    }

    public function logout()
    {
        Auth::logout();

        return Redirect::to('login')->with('message', 'You have successfully logged out.');
    }

    public function profile()
    {
        $user = Auth::user();

        $error = array();

        if(Request::isPost()) {
            // The requested new Password information.
            $password = Request::post('newPassword');
            $confirm  = Request::post('confirmPass');

            if (! Password::verify(Request::post('password'), $user->password)) {
                $error[] = 'The current Password is invalid.';
            } else if ($password != $confirm) {
                $error[] = 'The new Password and its verification are not equals.';
            } else if(! preg_match("/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password)) {
                $error[] = 'The new Password is not strong enough.';
            } else {
                $this->model->updateUser($user, array('password' => Password::make($password)));

                // Use a Redirect to avoid the reposting the data.
                return Redirect::to('profile')->with('message', 'You have successfully updated your Password.');
            }
        }

        return View::make('Users/Profile')
            ->shares('title', 'User Profile')
            ->with('user', $user)
            ->with('csrfToken', Csrf::makeToken())
            ->with('error', $error);
    }
}
