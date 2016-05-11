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
use Auth\Auth as Authorize;


class Auth extends Controller
{
    protected $layout = 'custom';


    public function __construct()
    {
        parent::__construct();
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
            if (Request::post('remember') && (Request::post('remember') == 'on')) {
                $remember = true;
            } else {
                $remember = false;
            }

            // Make an attempt to login the Guest with the given credentials.
            if(Authorize::attempt($credentials, $remember)) {
                // The User is authenticated now; retrieve his data as an (std)Class instance.
                $user = Authorize::user();

                // Prepare the flash message.
                $message = sprintf('<b>%s</b>, you have successfully logged in.', $user->realname);

                // Redirect to Users Dashboard.
                return Redirect::to('dashboard')->with('message', $message);
            } else {
                // Errors happened on authentication; add a message into $error array.
                $error[] = 'Wrong username or password';
            }
        }

        return View::make('Auth/Login')
            ->shares('title', 'Login')
            ->with('csrfToken', Csrf::makeToken())
            ->with('error', $error);
    }

    public function logout()
    {
        Authorize::logout();

        return Redirect::to('login')->with('message', 'You have successfully logged out.');
    }
}
