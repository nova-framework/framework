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
    public function createPermissions(array $items)
    {
        foreach ($items as $item) {
            $this->createPermission($item);
        }

        // Invalidate the cached system permissions.
        Cache::forget('system_permissions');
    }

    /**
     * Create a new Permission from an array of attributes.
     *
     * @return void
     */
    protected function createPermission(array $attributes)
    {
        $updateRoles = Schema::hasTable('role_permission');

        //
        extract($attributes);

        unset($attributes['roles']);

        // Update or create a new Permission instance.
        $permission = Permission::updateOrCreate(array('slug' => $slug), $attributes);

        if (isset($roles) && $updateRoles) {
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
        $updateRoles = Schema::hasTable('role_permission');

        $permissions = Permission::where('group', $group)->lists('slug');

        foreach ($permissions as $slug) {
            $this->deletePermission($slug, $updateRoles);
        }

        // Invalidate the cached system permissions.
        Cache::forget('system_permissions');
    }

    /**
     * Delete the Permission with the given slug.
     *
     * @return void
     */
    protected function deletePermission($slug, $updateRoles = true)
    {
        $permission = Permission::where('slug', $slug)->first();

        if (is_null($permission)) {
            return;
        } else if ($updateRoles) {
            $permission->roles()->dettach();
        }

        $permission->delete();
    }
}
