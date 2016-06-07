<?php
/**
 * Roles - A Controller for managing the Users Authorization.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Users\Controllers\Admin;

use Core\Config;
use Core\View;
use Helpers\Url;
use Helpers\ReCaptcha;

use App\Models\Role;
use App\Modules\Users\Core\BaseController;
use App\Modules\Users\Helpers\RoleVerifier as Authorize;

use Carbon\Carbon;

use Auth;
use Hash;
use Input;
use Redirect;
use Session;
use Validator;


class Roles extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // Prepare the Roles Model instance - while using the Database Auth Driver.
        //$this->model = new \App\Modules\Users\Models\Roles();
    }

    protected function before()
    {
        // Check the User Authorization - while using the Extended Auth Driver.
        if (! Auth::user()->hasRole('administrator')) {
            $status = __('You are not authorized to access this resource.');

            return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
        }

        // Check the User Authorization - while using the Database Auth Driver.
        /*
        if (! Authorize::userHasRole('administrator')) {
            $status = __('You are not authorized to access this resource.');

            return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
        }
        */

        // Leave to parent's method the Execution Flow decisions.
        return parent::before();
    }

    protected function validate(array $data, $id = null)
    {
        if (! is_null($id)) {
            $ignore = ',' .intval($id);
        } else {
            $ignore =  '';
        }

        // The Validation rules.
        $rules = array(
            'name'        => 'required|min:4|max:40|valid_name',
            'slug'        => 'required|min:4|max:40|alpha_dash|unique:roles,name' .$ignore,
            'description' => 'required|min:5|max:255',
        );

        $attributes = array(
            'name'        => __('Name'),
            'slug'        => __('Slug'),
            'description' => __('Description'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_name', function($attribute, $value, $parameters)
        {
            return (preg_match('/^[\p{L}\p{N}_\-\s]+$/', $value) === 1);
        });

        return Validator::make($data, $rules, array(), $attributes);
    }

    public function index()
    {
        // Get all Role records for current page - used with the Extended Auth Driver.
        $roles = Role::with('users')->paginate(25);

        // Get all Role records for current page - used with the Database Auth Driver.
        //$roles = $this->model->paginate(25);

        return $this->getView()
            ->shares('title', __('Roles'))
            ->with('roles', $roles);
    }

    public function create()
    {
        return $this->getView()
            ->shares('title', __('Create Role'));
    }

    public function store()
    {
        // Validate the Input data.
        $input = Input::only('name', 'slug', 'description');

        $validator = $this->validate($input);

        if($validator->passes()) {
            // Create a Role Model instance - used with the Extended Auth Driver.
            Role::create($input);

            // Create a Role Model instance - used with the Database Auth Driver.
            // $input['created_at'] = $input['created_at'] = new Carbon();
            //
            //$this->model->insert($input);

            // Prepare the flash message.
            $status = __('The Role <b>{0}</b> was successfully created.', $input['name']);

            return Redirect::to('admin/roles')->withStatus($status);
        }

        // Errors occurred on Validation.
        $status = $validator->errors();

        return Redirect::back()->withInput()->withStatus($status, 'danger');
    }

    public function show($id)
    {
        // Get the Role Model instance - used with the Extended Auth Driver.
        $role = Role::find($id);

        // Get the Role Model instance - used with the Database Auth Driver.
        //$role = $this->model->find($id);

        if($role === null) {
            // There is no Role with this ID.
            $status = __('Role not found: #{0}', $id);

            return Redirect::to('admin/roles')->withStatus($status, 'danger');
        }

        return $this->getView()
            ->shares('title', __('Show Role'))
            ->with('role', $role);
    }

    public function edit($id)
    {
        // Get the Role Model instance - used with the Extended Auth Driver.
        $role = Role::find($id);

        // Get the Role Model instance - used with the Database Auth Driver.
        //$role = $this->model->find($id);

        if($role === null) {
            // There is no Role with this ID.
            $status = __('Role not found: #{0}', $id);

            return Redirect::to('admin/roles')->withStatus($status, 'danger');
        }

        return $this->getView()
            ->shares('title', __('Edit Role'))
            ->with('role', $role);
    }

    public function update($id)
    {
        // Get the Role Model instance - used with the Extended Auth Driver.
        $role = Role::find($id);

        // Get the Role Model instance - used with the Database Auth Driver.
        //$role = $this->model->find($id);

        if($role === null) {
            // There is no Role with this ID.
            $status = __('Role not found: #{0}', $id);

            return Redirect::to('admin/roles')->withStatus($status, 'danger');
        }

        // Validate the Input data.
        $input = Input::only('name', 'slug', 'description');

        $validator = $this->validate($input, $id);

        if($validator->passes()) {
            $origName = $role->name;

            // Update the Role Model instance.
            $role->name        = $input['name'];
            $role->slug        = $input['slug'];
            $role->description = $input['description'];

            // Save the Role information - used with the Extended Auth Driver.
            $role->save();

            // Save the Role information - used with the Database Auth Driver.
            // $role->updated_at = new Carbon();
            //
            //$this->model->update($id, (array) $role);

            // Prepare the flash message.
            $status = __('The Role <b>{0}</b> was successfully updated.', $origName);

            return Redirect::to('admin/roles')->withStatus($status);
        }

        // Errors occurred on Validation.
        $status = $validator->errors();

        return Redirect::back()->withInput()->withStatus($status, 'danger');
    }

    public function destroy($id)
    {
        // Get the Role Model instance - used with the Extended Auth Driver.
        $role = Role::find($id);

        // Get the Role Model instance - used with the Database Auth Driver.
        //$role = $this->model->find($id);

        if($role === null) {
            // There is no Role with this ID.
            $status = __('Role not found: #{0}', $id);

            return Redirect::to('admin/roles')->withStatus($status, 'danger');
        }

        // Destroy the requested Role record - used with the Extended Auth Driver.
        $role->delete();

        // Destroy the requested Role record - used with the Database Auth Driver.
        //$this->model->delete($id);

        // Prepare the flash message.
        $status = __('The Role <b>{0}</b> was successfully deleted.', $role->name);

        return Redirect::to('admin/roles')->withStatus($status);
    }

}
