<?php

namespace App\Modules\Platform\Database;

use Nova\Support\Facades\Cache;

use App\Modules\Permissions\Models\Permission;


trait CreatePermissionsTrait
{

    /**
     * Install the given permissions.
     *
     * @return void
     */
    public function createPermissions(array $items)
    {
        foreach ($items as $item) {
            $permission = Permission::create(array(
                'name'  => $item['name'],
                'slug'  => $item['slug'],
                'group' => $item['group'],
            ));

            if (isset($item['roles'])) {
                $roles = $item['roles'];

                $permission->roles()->sync($roles);
            } else {
                $permission->roles()->detach();
            }
        }

        // Invalidate the cached system permissions.
        Cache::forget('system_permissions');
    }

    /**
     * Uninstall the permissions from the given group.
     *
     * @return void
     */
    public function deletePermissions($group)
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
