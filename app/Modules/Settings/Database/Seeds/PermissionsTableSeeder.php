<?php

namespace App\Modules\Settings\Database\Seeds;

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
                'name'  => 'Manage the Platform Settings',
                'slug'  => 'module.settings.manage',
                'group' => 'settings',

                'roles' => array(1, 2),
            ),
        );

        $this->installPermissions($permissions);
    }
}
