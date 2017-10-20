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

    protected function createPermission(array $attributes)
    {
        $updateRoles = Schema::hasTable('role_permission');

        // Extract the attributes.
        extract($attributes);

        // We will remove the Permission with this slug.
        $permission = Permission::where('slug', $slug)->first();

        if (! is_null($permission)) {
            if ($updateRoles) {
                $permission->roles()->dettach();
            }

            $permission->delete();
        }

        // Create the new Permission instance.
        $permission = Permission::create(array(
            'name'  => $name,
            'slug'  => $slug,
            'group' => $group,
        ));

        if (isset($roles) && $updateRoles) {
            if (! is_array($roles)) $roles = array($roles);

            $permission->roles()->sync($roles);
        }
    }

    /**
     * Uninstall the permissions from the given group.
     *
     * @return void
     */
    public function deletePermissions($group)
    {
        $detachRoles = Schema::hasTable('role_permission');

        $permissions = Permission::where('group', $group)->get();

        foreach ($permissions as $permission) {
            if ($detachRoles) {
                $permission->roles()->detach();
            }

            $permission->delete();
        }

        // Invalidate the cached system permissions.
        Cache::forget('system_permissions');
    }
}
