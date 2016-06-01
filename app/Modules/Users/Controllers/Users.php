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
use Validator;


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

        return $this->getView()
            ->shares('title',  __d('users', 'User Profile'))
            ->with('csrfToken', Session::token())
            ->with('user', $user);
    }

    public function postProfile()
    {
        $user = Auth::user();

        // Retrieve the Input data.
        $input = Input::only('current_password', 'password', 'password_confirmation');

        // Prepare the Validation Rules, Messages and Attributes.
        $rules = array(
            'current_password'      => 'required|valid_password',
            'password'              => 'required|strong_password',
            'password_confirmation' => 'required|same:password',
        );

        $messages = array(
            'valid_password'  => __d('users', 'The :attribute field is invalid.'),
            'strong_password' => __d('users', 'The :attribute field is not strong enough.'),
        );

        $attributes = array(
            'current_password'      => __d('users', 'Current Password'),
            'password'              => __d('users', 'New Password'),
            'password_confirmation' => __d('users', 'Password Confirmation'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_password', function($attribute, $value, $parameters) use ($user)
        {
            return Hash::check($value, $user->password);
        });

        Validator::extend('strong_password', function($attribute, $value, $parameters)
        {
            $pattern = "/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/";

            return (preg_match($pattern, $value) === 1);
        });

        // Create a Validator instance.
        $validator = Validator::make($input, $rules, $messages, $attributes);

        // Validate the Input.
        if ($validator->passes()) {
            $password = $input['password'];

            // Update the password on the User Model instance.
            $user->password = Hash::make($password);

            // Save the User Model instance - used with the Extended Auth Driver.
            $user->save();

            // Save the User Model instance - used with the Database Auth Driver.
            //$this->model->updateGenericUser($user);

            // Use a Redirect to avoid the reposting the data.
            $status = __d('users', 'You have successfully updated your Password.');

            return Redirect::back()->withStatus($status);
        }

        // Collect the Validation errors.
        $status = $validator->errors()->all();

        return Redirect::back()->withStatus($status, 'danger');
    }
}
