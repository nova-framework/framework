<?php
/**
 * Users - A Controller for managing the Users Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Users\Controllers;

use Core\Config;
use Core\Controller;
use Core\View;
use Helpers\Url;
use Helpers\ReCaptcha;

use Auth;
use Hash;
use Input;
use Redirect;
use Session;


class Users extends Controller
{
    protected $template = 'AdminLte';
    protected $layout   = 'backend';


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
        return $this->getView()
            ->shares('title', __d('users', 'Dashboard'));
    }

    public function profile()
    {
        $user = Auth::user();

        $error = Session::remove('error', array());

        return $this->getView()
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
