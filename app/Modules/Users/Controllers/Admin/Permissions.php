<?php

namespace App\Modules\Users\Controllers\Admin;

use Nova\Auth\Access\AuthorizationException;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Gate;
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
        // Authorize the current User.
        if (Gate::denies('manage', Permission::class)) {
            throw new AuthorizationException();
        }

        $modules = Module::enabled();

        $permissions = Permission::all();

        $roles = Role::all();

        return $this->createView()
            ->shares('title', __d('users', 'Permissions'))
            ->with('permissions', $permissions)
            ->with('roles', $roles)
            ->with('modules', $modules);
    }

    public function update(Request $request)
    {
        // Authorize the current User.
        if (Gate::denies('manage', Permission::class)) {
            throw new AuthorizationException();
        }

        $roleIds = Role::all()->lists('id');

        foreach ($request->input('permission_id', array()) as $id => $roles) {
            // We will filter the input variables, because of no validation.
            $id = intval($id);

            $roles = array_unique(array_map(function ($value)
            {
                return intval($value);

            }, (array) $roles));

            try {
                $permission = Permission::with('roles')->findOrFail($id);
            }
            catch (ModelNotFoundException $e) {
                continue;
            }

            if (! empty($roles)) {
                $permission->roles()->sync(array_intersect($roleIds, $roles));
            } else {
                $permission->roles()->detach();
            }
        }

        // Invalidate the cached system permissions.
        Cache::forget('system_permissions');

        // Prepare the flash message.
        $status = __d('users', 'The permissions was successfully updated.');

        return Redirect::to('admin/permissions')->withStatus($status);
    }
}
