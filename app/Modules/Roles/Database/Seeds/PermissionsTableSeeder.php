<?php

namespace App\Modules\Roles\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use App\Modules\Platform\Database\ManagePermissionsTrait;


class PermissionsTableSeeder extends Seeder
{
    use ManagePermissionsTrait;


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
                'slug'  => 'module.roles.lists',
                'group' => 'roles',

                'roles' => array(1, 2, 3),
            ),
            array(
                'name'  => 'View the Roles',
                'slug'  => 'module.roles.view',
                'group' => 'roles',

                'roles' => array(1, 2, 3),
            ),
            array(
                'name'  => 'Create new Roles',
                'slug'  => 'module.roles.create',
                'group' => 'roles',

                'roles' => array(1, 2),
            ),
            array(
                'name'  => 'Update the Roles',
                'slug'  => 'module.roles.update',
                'group' => 'roles',

                'roles' => array(1, 2),
            ),
            array(
                'name'  => 'Delete Roles',
                'slug'  => 'module.roles.delete',
                'group' => 'roles',

                'roles' => array(1, 2),
            ),
        );

        $this->createPermissions($permissions);
    }
}
