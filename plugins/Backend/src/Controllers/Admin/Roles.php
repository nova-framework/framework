<?php
/**
 * Roles - A Controller for managing the Users Authorization.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 */

namespace Backend\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Language;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\Validator;
use Nova\Support\Facades\View;

use Backend\Controllers\BaseController;
use Backend\Models\Role;


class Roles extends BaseController
{

    public function __construct()
    {
        $this->middleware('role:administrator');
    }

    protected function validator(array $data, $id = null)
    {
        if (! is_null($id)) {
            $ignore = ',' .intval($id);
        } else {
            $ignore =  '';
        }

        // The Validation rules.
        $rules = array(
            'name'        => 'required|min:4|max:40|valid_name',
            'slug'        => 'required|min:4|max:40|alpha_dash|unique:roles,slug' .$ignore,
            'description' => 'required|min:5|max:255',
        );

        $messages = array(
            'valid_name' => __d('backend', 'The :attribute field is not a valid name.'),
        );

        $attributes = array(
            'name'        => __d('backend', 'Name'),
            'slug'        => __d('backend', 'Slug'),
            'description' => __d('backend', 'Description'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_name', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){1,}$~u';

            return (preg_match($pattern, $value) === 1);
        });

        return Validator::make($data, $rules, $messages, $attributes);
    }

    public function data()
    {
        $columns = array(
            array('data' => 'roleid',    'field' => 'id'),
            array('data' => 'name',        'field' => 'name'),
            array('data' => 'slug',        'field' => 'slug'),
            array('data' => 'details',    'field' => 'description'),

            array('data' => 'users', 'uses' => function($role)
            {
                return $role->users->count();
            }),

            array('data' => 'actions', 'uses' => function($role)
            {
                return View::make('Backend::Partials/RolesTableActions')
                    ->with('role', $role)
                    ->render();
            }),
        );

        $input = Input::only('draw', 'columns', 'start', 'length', 'search', 'order');

        $query = Role::with('users');

        //
        $data = $this->dataTable($query, $input, $columns);

        return Response::json($data);
    }

    public function index()
    {
        $langInfo = Language::info();

        return $this->createView()
            ->shares('title', __d('backend', 'Roles'))
            ->with('langInfo', $langInfo);
    }

    public function create()
    {
        return $this->createView()
            ->shares('title', __d('backend', 'Create Role'));
    }

    public function store()
    {
        // Validate the Input data.
        $input = Input::only('name', 'slug', 'description');

        $validator = $this->validator($input);

        if($validator->passes()) {
            // Create a Role Model instance.
            Role::create($input);

            // Prepare the flash message.
            $status = __d('backend', 'The Role <b>{0}</b> was successfully created.', $input['name']);

            return Redirect::to('admin/roles')->with('success', $status);
        }

        // Errors occurred on Validation.
        return Redirect::back()->withInput()->withErrors($validator->errors());
    }

    public function show($id)
    {
        // Get the Role Model instance.
        try {
            $role = Role::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $status = __d('backend', 'The Role with ID: {0} was not found.', $id);

            return Redirect::to('admin/roles')->with('warning', $status);
        }

        return $this->createView()
            ->shares('title', __d('backend', 'Show Role'))
            ->with('role', $role);
    }

    public function edit($id)
    {
        // Get the Role Model instance.
        try {
            $role = Role::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $status = __d('backend', 'The Role with ID: {0} was not found.', $id);

            return Redirect::to('admin/roles')->with('warning', $status);
        }

        return $this->createView()
            ->shares('title', __d('backend', 'Edit Role'))
            ->with('role', $role);
    }

    public function update($id)
    {
        // Get the Role Model instance.
        try {
            $role = Role::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $status = __d('backend', 'The Role with ID: {0} was not found.', $id);

            return Redirect::to('admin/roles')->with('warning', $status);
        }

        // Validate the Input data.
        $input = Input::only('name', 'slug', 'description');

        $validator = $this->validator($input, $id);

        if($validator->passes()) {
            $origName = $role->name;

            // Update the Role Model instance.
            $role->name        = $input['name'];
            $role->slug        = $input['slug'];
            $role->description = $input['description'];

            // Save the Role information.
            $role->save();

            // Prepare the flash message.
            $status = __d('backend', 'The Role <b>{0}</b> was successfully updated.', $origName);

            return Redirect::to('admin/roles')->with('success', $status);
        }

        // Errors occurred on Validation.
        return Redirect::back()->withInput()->withErrors($validator->errors());
    }

    public function destroy($id)
    {
        // Get the Role Model instance.
        try {
            $role = Role::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $status = __d('backend', 'The Role with ID: {0} was not found.', $id);

            return Redirect::to('admin/roles')->with('warning', $status);
        }

        // Destroy the requested Role record.
        $role->delete();

        // Prepare the flash message.
        $status = __d('backend', 'The Role <b>{0}</b> was successfully deleted.', $role->name);

        return Redirect::to('admin/roles')->with('success', $status);
    }

}
