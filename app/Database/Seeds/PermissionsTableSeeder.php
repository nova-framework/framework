<?php

namespace App\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use App\Database\InstallPermissionsTrait;


class PermissionsTableSeeder extends Seeder
{
    use InstallPermissionsTrait;


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = array(
            // Roles.
            array(
                'name'  => 'View the Roles List',
                'slug'  => 'app.roles.lists',
                'group' => 'app',

                'roles' => array(1, 2, 3),
            ),
            array(
                'name'  => 'View the Roles',
                'slug'  => 'app.roles.view',
                'group' => 'app',

                'roles' => array(1, 2, 3),
            ),
            array(
                'name'  => 'Create new Roles',
                'slug'  => 'app.roles.create',
                'group' => 'app',

                'roles' => array(1),
            ),
            array(
                'name'  => 'Update the Roles',
                'slug'  => 'app.roles.update',
                'group' => 'app',

                'roles' => array(1),
            ),
            array(
                'name'  => 'Delete Roles',
                'slug'  => 'app.roles.delete',
                'group' => 'app',

                'roles' => array(1),
            ),

            // Permissions.
            array(
                'name'  => 'Manage the Permissions',
                'slug'  => 'app.permissions.manage',
                'group' => 'app',

                'roles' => array(1),
            ),

            // Users.
            array(
                'name'  => 'View the Users List',
                'slug'  => 'app.users.lists',
                'group' => 'app',

                'roles' => array(1, 2, 3, 4),
            ),
            array(
                'name'  => 'View the User accounts',
                'slug'  => 'app.users.view',
                'group' => 'app',

                'roles' => array(1, 2, 3, 4),
            ),
            array(
                'name'  => 'View his own User account',
                'slug'  => 'app.users.view.own',
                'group' => 'app',

                'roles' => array(1, 2, 3, 4),
            ),
            array(
                'name'  => 'Create new User accounts',
                'slug'  => 'app.users.create',
                'group' => 'app',

                'roles' => array(1, 2),
            ),
            array(
                'name'  => 'Update the User accounts',
                'slug'  => 'app.users.update',
                'group' => 'app',

                'roles' => array(1, 2),
            ),
            array(
                'name'  => 'Update his own User account',
                'slug'  => 'app.users.update.own',
                'group' => 'app',

                'roles' => array(1, 2, 3, 4),
            ),
            array(
                'name'  => 'Delete User accounts',
                'slug'  => 'app.users.delete',
                'group' => 'app',

                'roles' => array(1, 2),
            ),
            array(
                'name'  => 'Delete his own User account',
                'slug'  => 'app.users.delete.own',
                'group' => 'app',

                'roles' => array(1, 2, 3),
            ),
        );

        $this->installPermissions($permissions);
    }
}
