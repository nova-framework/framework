<?php
/**
 * Auth - A Controller for managing the User Authentication.
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
use Auth\Auth;


class Users extends Controller
{
    protected $layout = 'custom';


    public function __construct()
    {
        parent::__construct();
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

                // Redirect to Users Dashboard.
                return Redirect::to('dashboard')->with('message', $message);
            } else {
                // Errors happened on authentication; add a message into $error array.
                $error[] = 'Wrong username or password';
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
}
