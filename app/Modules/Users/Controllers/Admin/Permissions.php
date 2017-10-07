<?php

namespace App\Modules\Users\Controllers\Admin;

use Nova\Auth\Access\AuthorizationException;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Module;
use Nova\Support\Facades\Redirect;

use App\Models\Permission;
use App\Models\Role;
use App\Modules\System\Controllers\BaseController;


class Permissions extends BaseController
{

    public function index()
    {
        /*
        // Authorize the current User.
        if (Gate::denies('manage', Permission::class)) {
            throw new AuthorizationException();
        }
        */

        $permissions = Permission::all();

        $roles = Role::all();

        $modules = Module::all();

        return $this->createView()
            ->shares('title', __d('users', 'Permissions'))
            ->with('permissions', $permissions)
            ->with('roles', $roles)
            ->with('modules', $modules);
    }

    public function update(Request $request)
    {
        /*
        // Authorize the current User.
        if (Gate::denies('manage', Permission::class)) {
            throw new AuthorizationException();
        }
        */

        $permissionIds = Input::get('permission_id', array());

        foreach ($permissionIds as $id => $roles) {
            try {
                $permission = Permission::with('roles')->findOrFail($id);
            }
            catch (ModelNotFoundException $e) {
                $status = __d('users', 'Permission not found: #{0}', $id);

                return Redirect::to('admin/users')->withStatus($status, 'danger');
            }

            $permission->roles()->sync($roles);
        }

        // Prepare the flash message.
        $status = __d('users', 'The permissions was successfully updated.');

        return Redirect::to('admin/permissions')->withStatus($status);
    }
}
