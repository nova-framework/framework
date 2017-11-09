<?php
/**
 * Users - A Controller for managing the Users Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Users\Controllers\Admin;

use Nova\Http\Request;
use Nova\Auth\Access\AuthorizationException;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Gate;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\File;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;
use Nova\Support\Arr;

use App\Modules\Platform\Controllers\Admin\BaseController;
use App\Modules\Roles\Models\Role;
use App\Modules\Users\Models\Profile;
use App\Modules\Users\Models\User;

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
            'password'              => $required .'|confirmed|strong_password',
            'password_confirmation' => $required .'|same:password',
            'email'                 => 'required|min:5|max:100|email',
        );

        $messages = array(
            'valid_name'      => __d('users', 'The :attribute field is not a valid name.'),
            'strong_password' => __d('users', 'The :attribute field is not strong enough.'),
        );

        $attributes = array(
            'username'              => __d('users', 'Username'),
            'role'                  => __d('users', 'Role'),
            'password'              => __d('users', 'Password'),
            'password_confirmation' => __d('users', 'Password confirmation'),
            'email'                 => __d('users', 'E-mail'),
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

    public function index()
    {
        // Authorize the current User.
        if (Gate::denies('lists', User::class)) {
            throw new AuthorizationException();
        }

        // Get all User records for current page.
        $users = User::whereMeta('activated', 1)->paginate(25);

        return $this->createView()
            ->shares('title', __d('users', 'Users'))
            ->with('users', $users);
    }

    public function create(Request $request)
    {
        // Authorize the current User.
        if (Gate::denies('create', User::class)) {
            throw new AuthorizationException();
        }

        $profile = Profile::findOrFail(1);

        $fields = $profile->fields;

        // Get all available User Roles.
        $roles = Role::all();

        // The Custom Fields.
        $html = $fields->renderForEditor($request);

        return $this->createView()
            ->shares('title', __d('users', 'Create User'))
            ->with('roles', $roles)
            ->with('fields', $html);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        // Authorize the current User.
        if (Gate::denies('create', User::class)) {
            throw new AuthorizationException();
        }

        $profile = Profile::findOrFail(1);

        $fields = $profile->fields;

        // Validate the Input data.
        $validator = $this->validator($input);

        $fields->updateValidator($validator);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withStatus($validator->errors(), 'danger');
        }

        // Encrypt the given Password.
        $password = Hash::make($input['password']);

        // Create a User Model instance.
        $user = User::create(array(
            'username'   => $input['username'],
            'password'   => $password,
            'email'      => $input['email'],
            'profile_id' => $profile->id,
        ));

        // Update the Meta / Custom Fields.
        $user->load('meta');

        // Handle the meta-data.
        $input['activated'] = 1;

        $fields->updateMeta($user->meta, $input);

        $user->save();

        // Attach the Roles.
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

    public function edit(Request $request, $id)
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

        $fields = $user->profile->fields;

        // Get all available User Roles.
        $roles = Role::all();

        // The Custom Fields.
        $html = $fields->renderForEditor($request, $user->meta);

        return $this->createView()
            ->shares('title', __d('users', 'Edit User'))
            ->with('roles', $roles)
            ->with('fields', $html)
            ->with('user', $user);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        if(empty($input['password']) && empty($input['password_confirm'])) {
            unset($input['password'], $input['password_confirmation']);
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

        $fields = $user->profile->fields;

        // Validate the Input data.
        $validator = $this->validator($input, $id);

        $fields->updateValidator($validator);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withStatus($validator->errors(), 'danger');
        }

        // Update the User Model instance.
        $username = $user->username;

        //
        $user->username = $input['username'];
        $user->email    = $input['email'];

        if(isset($input['password'])) {
            // Encrypt and add the given Password.
            $user->password = Hash::make($input['password']);
        }

        // Update the Meta / Custom Fields.
        $fields->updateMeta($user->meta, $input);

        // Save the User information.
        $user->save();

        // Sync the Roles.
        $user->roles()->sync($input['roles']);

        // Invalidate the cached user roles and permissions.
        Cache::forget('user.roles.' .$id);
        Cache::forget('user.permissions.' .$id);

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

        // Invalidate the cached user roles and permissions.
        Cache::forget('user.roles.' .$id);
        Cache::forget('user.permissions.' .$user->id);

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
            ->orWhere('email', 'LIKE', '%' .$search .'%')
            ->paginate(15);

        // Prepare the Query for displaying.
        $search = htmlentities($search);

        return $this->createView()
            ->shares('title', __d('users', 'Searching Users for: {0}', $search))
            ->with('search', $search)
            ->with('users', $users);
    }
}
