<?php

namespace App\Modules\Permissions\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use App\Modules\Platform\Database\InstallPermissionsTrait;


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
            array(
                'name'  => 'Manage the Permissions',
                'slug'  => 'module.permissions.manage',
                'group' => 'permissions',

                'roles' => array(1),
            ),
        );

        $this->installPermissions($permissions);
    }
}
