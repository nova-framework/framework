<?php
/**
 * Users - A Controller for managing the Users Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Platform\Controllers;

use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Gate;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;
use Nova\Support\Facades\View;
use Nova\Support\Arr;

use App\Modules\Platform\Controllers\BaseController;
use App\Modules\Users\Models\User;


class Account extends BaseController
{
    /**
     * The currently used Layout.
     *
     * @var mixed
     */
    protected $layout = 'Frontend';


    protected function validator(array $data, User $user)
    {
        // Prepare the Validation Rules, Messages and Attributes.
        $rules = array(
            'current_password'      => 'required|valid_password',
            'password'              => 'sometimes|required|confirmed|strong_password',
            'password_confirmation' => 'sometimes|required|same:password',
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
        Validator::extend('valid_name', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){1,}$~u';

            return (preg_match($pattern, $value) === 1);
        });

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

    public function index(Request $request)
    {
        $user = Auth::user();

        // Get the Custom Fields.
        $fields = $user->profile->fields;

        // The Custom Fields.
        $fieldsHtml = $fields->renderForEditor($request, $user->meta);

        return $this->createView()
            ->shares('title',  __d('users', 'Account'))
            ->with('user', $user)
            ->with('fields', $fieldsHtml);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Get the Custom Fields.
        $fields = $user->profile->fields;

        // Retrieve the Input data.
        $input = $request->all();

        if(empty($input['password']) && empty($input['password_confirm'])) {
            unset($input['password'], $input['password_confirmation']);
        }

        // Create a Validator instance.
        $validator = $this->validator($input, $user);

        $fields->updateValidator($validator);

        // Validate the Input.
        if ($validator->fails()) {
            return Redirect::back()->withStatus($validator->errors(), 'danger');
        }

        if (isset($input['password'])) {
            $password = $input['password'];

            // Update the password on the User Model instance.
            $user->password = Hash::make($password);
        }

        // Update the Meta / Custom Fields.
        $fields->updateMeta($user->meta, $input);

        // Save the User Model instance.
        $user->save();

        // Use a Redirect to avoid the reposting the data.
        $status = __d('users', 'You have successfully updated your Account information.');

        return Redirect::back()->withStatus($status);
    }
}
