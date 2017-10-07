<?php

namespace App\Modules\Users\Controllers\Admin;

use Nova\Auth\Access\AuthorizationException;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Module;

use App\Models\Permission;
use App\Models\Role;
use App\Modules\System\Controllers\BaseController;


class Permissions extends BaseController
{

    public function index()
    {
        /*
        // Authorize the current User.
        if (Gate::denies('lists', Permission::class)) {
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

    public function update()
    {
        /*
        // Authorize the current User.
        if (Gate::denies('lists', Permission::class)) {
            throw new AuthorizationException();
        }
        */
    }
}
