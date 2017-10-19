<?php

namespace App\Modules\Platform\Database;

use Nova\Support\Facades\Cache;

use App\Modules\Permissions\Models\Permission;


trait InstallPermissionsTrait
{

    /**
     * Install the given permissions.
     *
     * @return void
     */
    public function installPermissions(array $items)
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
}
