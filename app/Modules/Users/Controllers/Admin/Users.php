<?php
/**
 * Users - A Controller for managing the Users Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Users\Controllers\Admin;

use Nova\Auth\Access\AuthorizationException;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Gate;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\File;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;

use App\Modules\System\Controllers\BaseController;
use App\Models\Role;
use App\Models\User;

use Carbon\Carbon;


class Users extends BaseController
{

    protected function validator(array $data, $id = null)
    {
        if (! is_null($id)) {
            $ignore = ',' .intval($id);

            $required = 'sometimes|required';
        } else {
            $ignore = '';

            $required = 'required';
        }

        // The Validation rules.
        $rules = array(
            'username'              => 'required|min:4|max:100|alpha_dash|unique:users,username' .$ignore,
            'roles'                 => 'required|array|exists:roles,id',
            'realname'              => 'required|min:5|max:100|valid_name',
            'password'              => $required .'|confirmed|strong_password',
            'password_confirmation' => $required .'|same:password',
            'email'                 => 'required|min:5|max:100|email',
            'image'                 => 'max:1024|mimes:png,jpg,jpeg,gif',
        );

        $messages = array(
            'valid_name'      => __d('users', 'The :attribute field is not a valid name.'),
            'strong_password' => __d('users', 'The :attribute field is not strong enough.'),
        );

        $attributes = array(
            'username'              => __d('users', 'Username'),
            'role'                  => __d('users', 'Role'),
            'realname'              => __d('users', 'Name and Surname'),
            'password'              => __d('users', 'Password'),
            'password_confirmation' => __d('users', 'Password confirmation'),
            'email'                 => __d('users', 'E-mail'),
            'image'                 => __d('users', 'Profile Picture'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_name', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){2,}$~u';

            return (preg_match($pattern, $value) === 1);
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
        // Authorize the current User.
        if (Gate::denies('lists', User::class)) {
            throw new AuthorizationException();
        }

        // Get all User records for current page.
        $users = User::where('activated', 1)->paginate(25);

        return $this->createView()
            ->shares('title', __d('users', 'Users'))
            ->with('users', $users);
    }

    public function create()
    {
        // Authorize the current User.
        if (Gate::denies('create', User::class)) {
            throw new AuthorizationException();
        }

        // Get all available User Roles.
        $roles = Role::all();

        return $this->createView()
            ->shares('title', __d('users', 'Create User'))
            ->with('roles', $roles);
    }

    public function store()
    {
        $input = Input::only(
            'username', 'roles', 'realname', 'password', 'password_confirmation', 'email', 'image'
        );

        // Authorize the current User.
        if (Gate::denies('create', User::class)) {
            throw new AuthorizationException();
        }

        // Validate the Input data.
        $validator = $this->validator($input);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withStatus($validator->errors(), 'danger');
        }

        // Encrypt the given Password.
        $password = Hash::make($input['password']);

        // Create a User Model instance.
        $user = User::create(array(
            'username'  => $input['username'],
            'password'  => $password,
            'realname'  => $input['realname'],
            'email'     => $input['email'],
            'image'     => Input::file('image'),
            'activated' => 1,
        ));

        $user->roles()->attach($input['roles']);

        // Prepare the flash message.
        $status = __d('users', 'The User <b>{0}</b> was successfully created.', $user->username);

        return Redirect::to('admin/users')->withStatus($status);
    }

    public function show($id)
    {
        // Get the User Model instance.
        try {
            $user = User::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            // There is no User with this ID.
            $status = __d('users', 'User not found: #{0}', $id);

            return Redirect::to('admin/users')->withStatus($status, 'danger');
        }

        // Authorize the current User.
        if (Gate::denies('view', $user)) {
            throw new AuthorizationException();
        }

        return $this->createView()
            ->shares('title', __d('users', 'Show User'))
            ->with('user', $user);
    }

    public function edit($id)
    {
        // Get the User Model instance.
        try {
            $user = User::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            // There is no User with this ID.
            $status = __d('users', 'User not found: #{0}', $id);

            return Redirect::to('admin/users')->withStatus($status, 'danger');
        }

        // Authorize the current User.
        if (Gate::denies('update', $user)) {
            throw new AuthorizationException();
        }

        // Get all available User Roles.
        $roles = Role::all();

        return $this->createView()
            ->shares('title', __d('users', 'Edit User'))
            ->with('roles', $roles)
            ->with('user', $user);
    }

    public function update($id)
    {
        $input = Input::only(
            'username', 'roles', 'realname', 'password', 'password_confirmation', 'email', 'image'
        );

        if(empty($input['password']) && empty($input['password_confirm'])) {
            unset($input['password']);
            unset($input['password_confirmation']);
        }

        // Get the User Model instance.
        try {
            $user = User::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            // There is no User with this ID.
            $status = __d('users', 'User not found: #{0}', $id);

            return Redirect::to('admin/users')->withStatus($status, 'danger');
        }

        // Authorize the current User.
        if (Gate::denies('update', $user)) {
            throw new AuthorizationException();
        }

        // Validate the Input data.
        $validator = $this->validator($input, $id);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withStatus($validator->errors(), 'danger');
        }

        // Update the User Model instance.
        $username = $user->username;

        //
        $user->username = $input['username'];
        $user->realname = $input['realname'];
        $user->email    = $input['email'];

        // If a file has been uploaded.
        if (Input::hasFile('image')) {
            $user->image = Input::file('image');
        }

        if(isset($input['password'])) {
            // Encrypt and add the given Password.
            $user->password = Hash::make($input['password']);
        }

        // Save the User information.
        $user->save();

        // Sync the Roles.
        $user->roles()->sync($input['roles']);

        // Prepare the flash message.
        $status = __d('users', 'The User <b>{0}</b> was successfully updated.', $username);

        return Redirect::to('admin/users')->withStatus($status);
    }

    public function destroy($id)
    {
        // Get the User Model instance.
        try {
            $user = User::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            // There is no User with this ID.
            $status = __d('users', 'User not found: #{0}', $id);

            return Redirect::to('admin/users')->withStatus($status, 'danger');
        }

        // Authorize the current User.
        if (Gate::denies('delete', $user)) {
            throw new AuthorizationException();
        }

        // Detach the Roles.
        $user->roles()->detach();

        // Destroy the requested User record.
        $user->delete();

        // Prepare the flash message.
        $status = __d('users', 'The User <b>{0}</b> was successfully deleted.', $user->username);

        return Redirect::to('admin/users')->withStatus($status);
    }

    public function search()
    {
        // Authorize the current User.
        if (Gate::denies('lists', User::class)) {
            throw new AuthorizationException();
        }

        // Validation rules
        $rules = array(
            'query' => 'required|min:4|valid_query'
        );

        $messages = array(
            'valid_query' => __d('users', 'The :attribute field is not a valid query string.'),
        );

        $attributes = array(
            'query' => __('Search Query'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_query', function($attribute, $value, $parameters)
        {
            return (preg_match('/^[\p{L}\p{N}_\-\s]+$/', $value) === 1);
        });

        // Validate the Input data.
        $input = Input::only('query');

        $validator = Validator::make($input, $rules, $messages, $attributes);

        if($validator->fails()) {
            return Redirect::back()->withStatus($validator->errors(), 'danger');
        }

        // Search the Records on Database.
        $search = $input['query'];

        $users = User::where('username', 'LIKE', '%' .$search .'%')
            ->orWhere('realname', 'LIKE', '%' .$search .'%')
            ->orWhere('email', 'LIKE', '%' .$search .'%')
            ->get();

        // Prepare the Query for displaying.
        $search = htmlentities($search);

        return $this->createView()
            ->shares('title', __d('users', 'Searching Users for: {0}', $search))
            ->with('search', $search)
            ->with('users', $users);
    }
}
