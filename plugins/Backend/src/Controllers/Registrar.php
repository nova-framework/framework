<?php
/**
 * Authorize - A Controller for managing the User Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace AcmeCorp\Backend\Controllers;

use Nova\Foundation\Auth\RegistersUsersTrait;
use Nova\Http\Request;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Validator;

use AcmeCorp\Backend\Controllers\BaseController;
use AcmeCorp\Backend\Models\User;
use AcmeCorp\Backend\Models\Role;


class Registrar extends BaseController
{
    use RegistersUsersTrait;

    //
    protected $layout = 'Auth';

    protected $redirectTo = 'admin/dashboard';


    protected function validator(Request $request)
    {
        $data = $request->all();

        // Validation rules.
        $rules = array(
            'username'   => 'required|min:6|unique:users',
            'first_name' => 'required|min:4|max:100|valid_name',
            'last_name'  => 'required|min:4|max:100|valid_name',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|confirmed|strong_password'
        );

        $messages = array(
            'valid_name'      => __d('backend', 'The :attribute field is not a valid name.'),
            'strong_password' => __d('backend', 'The :attribute field is not strong enough.'),
        );

        $attributes = array(
            'username'   => __d('backend', 'Username'),
            'first_name' => __d('backend', 'First Name'),
            'last_name'  => __d('backend', 'Last Name'),
            'email'      => __d('backend', 'E-mail'),
            'password'   => __d('backend', 'Password'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_name', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){1,}$~u';

            return (preg_match($pattern, $value) === 1);
        });

        Validator::extend('strong_password', function($attribute, $value, $parameters)
        {
            $pattern = "/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/";

            return (preg_match($pattern, $value) === 1);
        });

        return Validator::make($data, $rules, $messages, $attributes);
    }

    protected function create($input)
    {
        // Encrypt the given Password.
        $password = Hash::make($input['password']);

        // Retrieve the default 'user' Role.
        $role = Role::where('slug', 'user')->first();

        return User::create(array(
            'username'   => $input['username'],
            'first_name' => $input['first_name'],
            'last_name'  => $input['last_name'],
            'email'      => $input['email'],
            'password'   => $password,
            'role_id'    => $role->getKey(),
        ));
    }
}
