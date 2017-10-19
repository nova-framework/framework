<?php

namespace App\Modules\Platform\Database;

use Nova\Database\QueryException;
use Nova\Support\Facades\Cache;

use App\Modules\Permissions\Models\Permission;


trait UninstallPermissionsTrait
{

    /**
     * Uninstall the permissions from the given group.
     *
     * @return void
     */
    public function uninstallPermissions($group)
    {
        try {
            $permissions = Permission::where('group', $group)->get();

            foreach ($permissions as $permission) {
                $permission->roles()->detach();

                $permission->delete();
            }
        }
        catch (QueryException $e) {
            //
        }

        // Invalidate the cached system permissions.
        Cache::forget('system_permissions');
    }
}
