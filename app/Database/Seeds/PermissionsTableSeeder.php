<?php

namespace App\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use App\Models\Permission;


class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = array(
            array(
                'name'  => 'Manage the Permissions',
                'slug'  => 'app.permissions.manage',
                'group' => 'app',

                'roles' => array(1),
            ),
            array(
                'name'  => 'View the Roles List',
                'slug'  => 'app.roles.lists',
                'group' => 'app',

                'roles' => array(1, 2, 3),
            ),
        );

        // Truncate the table before seeding.
        Permission::where('group', 'app')->delete();

        foreach ($permissions as $config) {
            $permission = Role::create(array(
                'name'  => $config['name'],
                'slug'  => $config['slug'],
                'group' => $config['group'],
            ));

            if (isset($config['roles'])) {
                $roles = $config['roles'];

                $permission->roles()->sync($roles);
            }
        }
    }
}
