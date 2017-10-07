<?php

namespace App\Modules\System\Database\Seeds;

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
            array(
                'name'  => 'Manage the Platform Settings',
                'slug'  => 'app.settings.manage',
                'group' => 'system',

                'roles' => array(1, 2),
            ),
        );

        $this->installPermissions($permissions);
    }
}
