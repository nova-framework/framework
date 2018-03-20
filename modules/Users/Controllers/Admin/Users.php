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
use Nova\Database\ORM\Collection;
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
use Nova\Support\Str;

use Modules\Platform\Controllers\Admin\BaseController;
use Modules\Roles\Models\Role;
use Modules\Users\Models\Field;
use Modules\Users\Models\FieldItem;
use Modules\Users\Models\User;

use Carbon\Carbon;


class Users extends BaseController
{

    protected function validator(array $data, Collection $items, $id = null)
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
            'realname'              => 'required|valid_name',
            'email'                 => 'required|min:5|max:100|email',
            'image'                 => 'max:1024|mimes:png,jpg,jpeg,gif'
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
            'realname'              => __d('users', 'Name and Surname'),
            'email'                 => __d('users', 'E-mail'),
            'image'                 => __d('users', 'Profile Picture'),
        );

        // Prepare the dynamic rules and attributes for Field Items.
        foreach ($items as $item) {
            if (empty($rule = $item->rule)) {
                continue;
            }

            $key = str_replace('-', '_', $item->name);

            if (isset($data[$key]) && empty($data[$key]) && Str::contains($rule, 'sometimes')) {
                unset($data[$key]);
            }

            $options = $item->options ?: array();

            if ($item->type == 'checkbox') {
                $choices = array_filter(explode("\n", trim(
                    Arr::get($options, 'choices')

                )), function ($value)
                {
                    return ! empty($value);
                });

                $count = count($choices);

                if ($count > 1) {
                    foreach (range(0, $count - 1) as $index) {
                        $name = $key .'.' .$index;

                        //
                        $rules[$name] = $rule;

                        $attributes[$name] = $item->title;
                    }

                    if (Str::contains($rule, 'required')) {
                        $rule = 'required|array';
                    } else {
                        $rule = 'array';
                    }
                }
            }

            $rules[$key] = $rule;

            $attributes[$key] = $item->title;
        }

        // Create a Validator instance.
        $validator = Validator::make($data, $rules, $messages, $attributes);

        // Add the custom Validation Rule commands.
        $validator->addExtension('valid_name', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){2,}$~u';

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

        $roles = Role::all();

        $items = FieldItem::orderBy('order', 'asc')->get();

        return $this->createView()
            ->shares('title', __d('users', 'Create User'))
            ->with('roles', $roles)
            ->with('items', $items);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        // Authorize the current User.
        if (Gate::denies('create', User::class)) {
            throw new AuthorizationException();
        }

        $items = FieldItem::all();

        // Validate the Input data.
        $validator = $this->validator($input, $items);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        // Encrypt the given Password.
        $password = Hash::make($input['password']);

        // Create a User Model instance.
        $user = User::create(array(
            'username'        => $input['username'],
            'password'        => $password,
            'realname'        => $input['realname'],
            'email'           => $input['email'],
            'activated'       => 1,
            'activation_code' => null,
        ));

        // Attach the Roles.
        $user->roles()->attach($input['roles']);

        // If a file has been uploaded...
        if ($request->hasFile('image')) {
            $user->image = $request->file('image');

            $user->save();
        }

        // Update the Custom Fields.
        foreach ($items as $item) {
            $value = Arr::get($input, $name = $item->name);

            $field = Field::create(array(
                'name'  => $name,
                'type'  => $item->type,
                'value' => $value,

                // Resolve the relationships.
                'field_item_id' => $item->id,
                'user_id'       => $user->id,
            ));
        }

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
            return Redirect::to('admin/users')->with('danger', _d('users', 'User not found: #{0}', $id));
        }

        // Authorize the current User.
        if (Gate::denies('update', $user)) {
            throw new AuthorizationException();
        }

        $roles = Role::all();

        $items = FieldItem::orderBy('order', 'asc')->get();

        return $this->createView()
            ->shares('title', __d('users', 'Edit User'))
            ->with('roles', $roles)
            ->with('items', $items)
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
            $user = User::with('fields', 'meta')->findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            // There is no User with this ID.
            return Redirect::to('admin/users')->with('danger', __d('users', 'User not found: #{0}', $id));
        }

        // Authorize the current User.
        if (Gate::denies('update', $user)) {
            throw new AuthorizationException();
        }

        $items = FieldItem::all();

        // Validate the Input data.
        $validator = $this->validator($input, $items, $id);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        // Update the User Model instance.
        $username = $user->username;

        //
        $user->username = $input['username'];
        $user->realname = $input['realname'];
        $user->email    = $input['email'];

        if(isset($input['password'])) {
            // Encrypt and add the given Password.
            $user->password = Hash::make($input['password']);
        }

        // If a file has been uploaded...
        if ($request->hasFile('image')) {
            $user->image = $request->file('image');
        }

        // Save the User information.
        $user->save();

        // Sync the Roles.
        $user->roles()->sync($input['roles']);

        // Update the Custom Fields.
        foreach ($items as $item) {
            $value = Arr::get($input, $name = $item->name);

            if (! is_null($field = $user->fields->findBy('name', $name))) {
                $field->value = $value;

                $field->save();

                continue;
            }

            $field = Field::create(array(
                'name'  => $name,
                'type'  => $item->type,
                'value' => $value,

                // Resolve the relationships.
                'field_item_id' => $item->id,
                'user_id'       => $user->id,
            ));
        }

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
            ->orWhere('realname', 'LIKE', '%' .$search .'%')
            ->paginate(15);

        // Prepare the Query for displaying.
        $search = htmlentities($search);

        return $this->createView()
            ->shares('title', __d('users', 'Searching Users for: {0}', $search))
            ->with('search', $search)
            ->with('users', $users);
    }
}
