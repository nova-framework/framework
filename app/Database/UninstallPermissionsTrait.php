<?php

namespace App\Database;

use Nova\Support\Facades\Cache;

use App\Models\Permission;


trait UninstallPermissionsTrait
{

    /**
     * Uninstall the permissions from the given group.
     *
     * @return void
     */
    public function uninstallPermissions($group)
    {
        $permissions = Permission::where('group', $group)->get();

        foreach ($permissions as $permission) {
            $permission->roles()->detach();

            $permission->delete();
        }

        // Invalidate the cached system permissions.
        Cache::forget('system_permissions');
    }
}
