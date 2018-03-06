<?php
/**
 * Users - A Controller for managing the Users Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Modules\Users\Controllers\Admin;

use Nova\Http\Request;
use Nova\Auth\Access\AuthorizationException;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Event;
use Nova\Support\Facades\Gate;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\File;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;
use Nova\Support\Arr;

use Modules\Platform\Controllers\Admin\BaseController;
use Modules\Roles\Models\Role;
use Modules\Users\Models\Profile;
use Modules\Users\Models\User;

use Modules\Users\Events\MetaFields\UpdateUserValidation;
use Modules\Users\Events\MetaFields\UserEditing;
use Modules\Users\Events\MetaFields\UserSaved;
use Modules\Users\Events\MetaFields\UserShowing;

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

        // Create a Validator instance.
        $validator = Validator::make($data, $rules, $messages, $attributes);

        // Add the custom Validation Rule commands.
        $validator->addExtension('valid_name', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){1,}$~u';

            return (preg_match($pattern, $value) === 1);
        });

        $validator->addExtension('strong_password', function($attribute, $value, $parameters)
        {
            $pattern = "/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/";

            return (preg_match($pattern, $value) === 1);
        });

        return $validator;
    }

    public function index()
    {
        // Authorize the current User.
        if (Gate::denies('lists', User::class)) {
            throw new AuthorizationException();
        }

        // Get all User records for current page.
        $users = User::hasMeta('activated', 1)->paginate(25);

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

        // Get all available User Roles.
        $roles = Role::all();

        // Handle the User's Meta Fields.
        $fields = $this->renderMetaFieldsForEditor();

        return $this->createView()
            ->shares('title', __d('users', 'Create User'))
            ->with('roles', $roles)
            ->with('fields', $fields);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        // Authorize the current User.
        if (Gate::denies('create', User::class)) {
            throw new AuthorizationException();
        }

        // Validate the Input data.
        $validator = $this->validator($input);

        Event::dispatch(new UpdateUserValidation($validator, $user));

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        // Encrypt the given Password.
        $password = Hash::make($input['password']);

        // Create a User Model instance.
        $user = User::create(array(
            'username'   => $input['username'],
            'password'   => $password,
            'email'      => $input['email'],
        ));

        // Attach the Roles.
        $user->roles()->attach($input['roles']);

        // Handle the meta fields associated to User Picture and its activation.
        $user->saveMeta(array(
            'picture'         => null,
            'activated'       => 1,
            'activation_code' => null,
        ));

        // Update the other Meta / Custom Fields.
        Event::dispatch(new UserSaved($user));

        return Redirect::to('admin/users')
            ->with('success', __d('users', 'The User <b>{0}</b> was successfully created.', $user->username));
    }

    public function show($id)
    {
        // Get the User Model instance.
        try {
            $user = User::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            // There is no User with this ID.
            return Redirect::to('admin/users')->with('danger', __d('users', 'User not found: #{0}', $id));
        }

        // Authorize the current User.
        if (Gate::denies('view', $user)) {
            throw new AuthorizationException();
        }

        // Handle the User's Meta Fields.
        $fields = $this->fetchMetaFields($user);

        return $this->createView()
            ->shares('title', __d('users', 'Show User'))
            ->with('fields', $fields)
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
            return Redirect::to('admin/users')->with('danger', _d('users', 'User not found: #{0}', $id));
        }

        // Authorize the current User.
        if (Gate::denies('update', $user)) {
            throw new AuthorizationException();
        }

        // Get all available User Roles.
        $roles = Role::all();

        // Handle the User's Meta Fields.
        $fields = $this->renderMetaFieldsForEditor($user);

        return $this->createView()
            ->shares('title', __d('users', 'Edit User'))
            ->with('roles', $roles)
            ->with('fields', $fields)
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
            return Redirect::to('admin/users')->with('danger', __d('users', 'User not found: #{0}', $id));
        }

        // Authorize the current User.
        if (Gate::denies('update', $user)) {
            throw new AuthorizationException();
        }

        // Validate the Input data.
        $validator = $this->validator($input, $id);

        Event::dispatch(new UpdateUserValidation($validator, $user));

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
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

        // Save the User information.
        $user->save();

        // Sync the Roles.
        $user->roles()->sync($input['roles']);

        // Update the Meta / Custom Fields.
        Event::dispatch(new UserSaved($user));

        // Invalidate the cached user roles and permissions.
        Cache::forget('user.roles.' .$id);
        Cache::forget('user.permissions.' .$id);

        return Redirect::to('admin/users')
            ->with('success', __d('users', 'The User <b>{0}</b> was successfully updated.', $username));
    }

    public function destroy($id)
    {
        // Get the User Model instance.
        try {
            $user = User::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            // There is no User with this ID.
            return Redirect::to('admin/users')->with('danger', __d('users', 'User not found: #{0}', $id));
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

        return Redirect::to('admin/users')
            ->with('success', __d('users', 'The User <b>{0}</b> was successfully deleted.', $user->username));
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
            return Redirect::back()->withErrors($validator);
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

    protected function renderMetaFieldsForEditor(User $user = null)
    {
        $responses = Event::dispatch(new UserEditing($user));

        return implode("\n", array_filter($responses, function ($response)
        {
            return ! is_null($response);
        }));
    }

    protected function fetchMetaFields(User $user = null)
    {
        $responses = Event::dispatch(new UserShowing($user));

        //
        $result = array();

        foreach ($responses as $response) {
            if (is_array($response) && ! empty($response)) {
                $result = array_merge($result, $response);
            }
        }

        return $result;
    }
}
