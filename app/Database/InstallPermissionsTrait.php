<?php

namespace App\Database;

use Nova\Support\Facades\Cache;

use App\Models\Permission;


trait InstallPermissionsTrait
{

    /**
     * Install the given permissions.
     *
     * @return void
     */
    public function installPermissions(array $items)
    {
        // Remove the old permissions before seeding.
        $permissions = Permission::where('group', 'system')->get();

        foreach ($permissions as $permission) {
            $permission->roles()->detach();

            $permission->delete();
        }

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
}
