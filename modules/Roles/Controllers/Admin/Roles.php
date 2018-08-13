<?php
/**
 * Roles - A Controller for managing the Users Authorization.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Modules\Roles\Controllers\Admin;

use Nova\Auth\Access\AuthorizationException;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Gate;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;
use Nova\Support\Facades\View;
use Nova\Support\Str;

use Shared\Support\Facades\DataTable;

use Modules\Platform\Controllers\Admin\BaseController;
use Modules\Roles\Models\Role;

use Carbon\Carbon;


class Roles extends BaseController
{

    protected function validator(array $data, $id = null)
    {
        $ignore = ! is_null($id) ? ',' .intval($id) : '';

        // The Validation rules.
        $rules = array(
            'name'        => 'required|min:4|max:40|valid_name',
            'slug'        => 'required|min:4|max:40|alpha_dash|unique:roles,slug' .$ignore,
            'description' => 'required|min:5|max:255',
        );

        $messages = array(
            'valid_name' => __d('roles', 'The :attribute field is not a valid name.'),
        );

        $attributes = array(
            'name'        => __d('roles', 'Name'),
            'slug'        => __d('roles', 'Slug'),
            'description' => __d('roles', 'Description'),
        );

        // Create a Validator instance.
        $validator = Validator::make($data, $rules, $messages, $attributes);

        // Add the custom Validation Rule commands.
        $validator->addExtension('valid_name', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){1,}$~u';

            return (preg_match($pattern, $value) === 1);
        });

        return $validator;
    }

    public function data(Request $request)
    {
        // Authorize the current User.
        if (Gate::denies('lists', Role::class)) {
            throw new AuthorizationException();
        }

        $query = Role::withCount('users');

        $dataTable = DataTable::make($query)
            ->column('id')
            ->column('name')
            ->column('slug');

        $dataTable->column('description', function ($role)
        {
            $content = Str::limit($title = $role->description, 80);

            return sprintf('<div title="%s">%s</div>', $title, $content);
        });

        $dataTable->column('users', 'users_count');

        $dataTable->column('actions', function ($role)
        {
            return View::fetch('Modules/Roles::Partials/RolesTableActions', compact('role'));
        });

        return $dataTable->handle($request);
    }

    public function index()
    {
        // Authorize the current User.
        if (Gate::denies('lists', Role::class)) {
            throw new AuthorizationException();
        }

        return $this->createView()
            ->shares('title', __d('roles', 'Roles'));
    }

    public function create()
    {
        // Authorize the current User.
        if (Gate::denies('create', Role::class)) {
            throw new AuthorizationException();
        }

        return $this->createView()
            ->shares('title', __d('roles', 'Create Role'));
    }

    public function store()
    {
        $input = Input::only('name', 'slug', 'description');

        // Authorize the current User.
        if (Gate::denies('create', Role::class)) {
            throw new AuthorizationException();
        }

        // Validate the Input data.
        $validator = $this->validator($input);

        if($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator->errors());
        }

        // Create a Role Model instance.
        Role::create($input);

        return Redirect::to('admin/roles')
            ->with('success', __d('roles', 'The Role <b>{0}</b> was successfully created.', $input['name']));
    }

    public function show($id)
    {
        // Get the Role Model instance.
        try {
            $role = Role::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            // There is no Role with this ID.
            return Redirect::to('admin/roles')->with('danger', __d('roles', 'Role not found: #{0}', $id));
        }

        // Authorize the current User.
        if (Gate::denies('view', $role)) {
            throw new AuthorizationException();
        }

        return $this->createView()
            ->shares('title', __d('roles', 'Show Role'))
            ->with('role', $role);
    }

    public function edit($id)
    {
        // Get the Role Model instance.
        try {
            $role = Role::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            // There is no Role with this ID.
            return Redirect::to('admin/roles')->with('danger', __('Role not found: #{0}', $id));
        }

        // Authorize the current User.
        if (Gate::denies('update', $role)) {
            throw new AuthorizationException();
        }

        return $this->createView()
            ->shares('title', __d('roles', 'Edit Role'))
            ->with('role', $role);
    }

    public function update($id)
    {
        $input = Input::only('name', 'slug', 'description');

        // Get the Role Model instance.
        try {
            $role = Role::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            // There is no Role with this ID.
            return Redirect::to('admin/roles')->with('danger', __d('roles', 'Role not found: #{0}', $id));
        }

        // Authorize the current User.
        if (Gate::denies('update', $role)) {
            throw new AuthorizationException();
        }

        // Validate the Input data.
        $validator = $this->validator($input, $id);

        if($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator->errors());
        }

        // Update the Role Model instance.
        $name = $role->name;

        //
        $role->name        = $input['name'];
        $role->slug        = $input['slug'];
        $role->description = $input['description'];

        // Save the Role information.
        $role->save();

        return Redirect::to('admin/roles')
            ->with('success', __d('roles', 'The Role <b>{0}</b> was successfully updated.', $name));
    }

    public function destroy($id)
    {
        // Get the Role Model instance.
        try {
            $role = Role::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            // There is no Role with this ID.
            return Redirect::to('admin/roles')->with('danger', __d('roles', 'Role not found: #{0}', $id));
        }

        // Authorize the current User.
        if (Gate::denies('delete', $role)) {
            throw new AuthorizationException();
        }

        // Destroy the requested Role record.
        $role->delete();

        return Redirect::to('admin/roles')
            ->with('success', __d('roles', 'The Role <b>{0}</b> was successfully deleted.', $role->name));
    }

}
