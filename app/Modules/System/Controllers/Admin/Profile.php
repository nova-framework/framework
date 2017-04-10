<?php
/**
 * Users - A Controller for managing the Users Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\System\Controllers\Admin;

use App\Core\BackendController;
use App\Models\User;

use Auth;
use Hash;
use Input;
use Redirect;
use Validator;
use View;


class Profile extends BackendController
{

    protected function validator(array $data, User $user)
    {
        // Prepare the Validation Rules, Messages and Attributes.
        $rules = array(
            'current_password'      => 'required|valid_password',
            'password'              => 'required|strong_password',
            'password_confirmation' => 'required|same:password',
        );

        $messages = array(
            'valid_password'  => __d('system', 'The :attribute field is invalid.'),
            'strong_password' => __d('system', 'The :attribute field is not strong enough.'),
        );

        $attributes = array(
            'current_password'      => __d('system', 'Current Password'),
            'password'              => __d('system', 'New Password'),
            'password_confirmation' => __d('system', 'Password Confirmation'),
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

        return Validator::make($data, $rules, $messages, $attributes);
    }

    public function index()
    {
        $user = Auth::user();

        return $this->getView()
            ->shares('title',  __d('system', 'User Profile'))
            ->with('user', $user);
    }

    public function update()
    {
        $user = Auth::user();

        // Retrieve the Input data.
        $input = Input::only('current_password', 'password', 'password_confirmation');

        // Create a Validator instance.
        $validator = $this->validator($input, $user);

        // Validate the Input.
        if ($validator->passes()) {
            $password = $input['password'];

            // Update the password on the User Model instance.
            $user->password = Hash::make($password);

            // Save the User Model instance.
            $user->save();

            // Use a Redirect to avoid the reposting the data.
            $status = __d('system', 'You have successfully updated your Password.');

            return Redirect::back()->withStatus($status);
        }

        // Collect the Validation errors.
        $status = $validator->errors()->all();

        return Redirect::back()->withStatus($status, 'danger');
    }

}
