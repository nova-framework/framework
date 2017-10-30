<?php

namespace App\Modules\Platform\Database;

use Nova\Database\QueryException;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Schema;

use App\Modules\Permissions\Models\Permission;


trait ManagePermissionsTrait
{

    /**
     * Create permissions from the given array of permission attributes.
     *
     * @return void
     */
    public function createPermissions(array $permissions)
    {
        $updateRoles = Schema::hasTable('permission_role');

        foreach ($permissions as $permission) {
            $this->createPermission($permission, $updateRoles);
        }

        // Invalidate the cached system permissions.
        Cache::forget('system_permissions');
    }

    /**
     * Create a new Permission from an array of attributes.
     *
     * @return void
     */
    protected function createPermission(array $data, $updateRoles)
    {
        $slug = $data['slug'];

        $attributes = array_except($data, array('roles', 'slug'));

        $permission = Permission::updateOrCreate(array('slug' => $slug), $attributes);

        if (isset($data['roles']) && $updateRoles) {
            $roles = $data['roles'];

            if (! is_array($roles)) $roles = array($roles);

            $permission->roles()->sync($roles);
        }
    }

    /**
     * Delete all permissions from the given group.
     *
     * @return void
     */
    public function deletePermissions($group)
    {
        $updateRoles = Schema::hasTable('permission_role');

        $permissions = Permission::where('group', $group)->get();

        foreach ($permissions as $permission) {
            if ($updateRoles) {
                $permission->roles()->detach();
            }

            $permission->delete();
        }

        // Invalidate the cached system permissions.
        Cache::forget('system_permissions');
    }
}
