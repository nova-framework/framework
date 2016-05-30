<?php
/**
 * Users - A Controller for managing the Users Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Users\Controllers;

use Core\Controller;
use Core\View;
use Helpers\Url;

use Auth;
use Hash;
use Input;
use Redirect;
use Session;


class Users extends Controller
{
    protected $layout = 'custom';


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

    public function dashboard()
    {
        return View::make('Users/Dashboard', 'Users')
            ->shares('title', __d('users', 'Dashboard'));
    }

    public function login()
    {
        $error = Session::remove('error', array());

        //View::share('js', 'https://www.google.com/recaptcha/api.js');

        return View::make('Users/Login', 'Users')
            ->shares('title', __d('users', 'User Login'))
            ->with('csrfToken', Session::token())
            ->with('error', $error);
    }

    public function postLogin()
    {
        $error = array();

        // Retrieve the Authentication credentials.
        $credentials = Input::only('username', 'password');

        // Prepare the 'remember' parameter.
        $remember = (Input::get('remember') == 'on');

        // Make an attempt to login the Guest with the given credentials.
        if(Auth::attempt($credentials, $remember)) {
            // The User is authenticated now; retrieve his Model instance.
            $user = Auth::user();

            // Prepare the flash message.
            $message = __d('users', '<b>{0}</b>, you have successfully logged in.', $user->username);

            // Redirect to the User's Dashboard.
            return Redirect::to('dashboard')->with('message', $message);
        }

        // An error has happened on authentication; add a message into $error array.
        $error[] = __d('users', 'Wrong username or password.');

        return Redirect::back()->with('error', $error);
    }

    public function logout()
    {
        Auth::logout();

        return Redirect::to('login')->with('message', __d('users', 'You have successfully logged out.'));
    }

    public function profile()
    {
        $user = Auth::user();

        $error = Session::remove('error', array());

        return View::make('Users/Profile', 'Users')
            ->shares('title',  __d('users', 'User Profile'))
            ->with('user', $user)
            ->with('csrfToken', Session::token())
            ->with('error', $error);
    }

    public function postProfile()
    {
        $user = Auth::user();

        $error = array();

        // The requested new Password information.
        $password = Input::get('password');
        $confirm  = Input::get('password_confirmation');

        if (! Hash::check(Input::get('current_password'), $user->password)) {
            $error[] = __d('users', 'The current Password is invalid.');
        } else if ($password != $confirm) {
            $error[] = __d('users', 'The new Password and its verification are not equals.');
        } else if(! preg_match("/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password)) {
            $error[] = __d('users', 'The new Password is not strong enough.');
        } else {
            $user->password = Hash::make($password);

            // Save the User Model instance - used with the Extended Auth Driver.
            $user->save();

            // Save the User Model instance - used with the Database Auth Driver.
            //$this->model->updateGenericUser($user);

            // Use a Redirect to avoid the reposting the data.
            return Redirect::back()->with('message', __d('users', 'You have successfully updated your Password.'));
        }

        return Redirect::back()->with('error', $error);
    }
}
