<?php

namespace App\Modules\Files\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;
use Nova\Support\Facades\Cache;

use App\Models\Permission;


class UpdatePermissions extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = array(
            array(
                'name'  => 'Manage the Files',
                'slug'  => 'module.files.manage',
                'group' => 'files',

                'roles' => array(1, 2, 3),
            ),
            array(
                'name'  => 'View the Site Root',
                'slug'  => 'module.files.site.view',
                'group' => 'files',

                'roles' => array(1),
            ),
        );

        // Remove the old permissions before seeding.
        $permissions = Permission::where('group', 'files')->get();

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
