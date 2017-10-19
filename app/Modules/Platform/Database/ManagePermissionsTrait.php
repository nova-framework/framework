<?php

namespace App\Modules\Platform\Database;

use Nova\Database\QueryException;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Schema;

use App\Modules\Permissions\Models\Permission;


trait ManagePermissionsTrait
{

    /**
     * Install the given permissions.
     *
     * @return void
     */
    public function createPermissions(array $items)
    {
        foreach ($items as $item) {
            $this->createPermission($item);
        }

        // Invalidate the cached system permissions.
        Cache::forget('system_permissions');
    }

    protected function createPermission(array $permission)
    {
        $instance = Permission::create(array(
            'name'  => $permission['name'],
            'slug'  => $permission['slug'],
            'group' => $permission['group'],
        ));

        if (! Schema::hasTable('role_permission')) {
            return;
        }

        $roles = isset($permission['roles']) ? $permission['roles'] : array();

        $instance->roles()->sync($roles);
    }

    /**
     * Uninstall the permissions from the given group.
     *
     * @return void
     */
    public function deletePermissions($group)
    {
        $permissions = Permission::where('group', $group)->get();

        foreach ($permissions as $permission) {
            if (Schema::hasTable('role_permission')) {
                $permission->roles()->detach();
            }

            $permission->delete();
        }

        // Invalidate the cached system permissions.
        Cache::forget('system_permissions');
    }
}
