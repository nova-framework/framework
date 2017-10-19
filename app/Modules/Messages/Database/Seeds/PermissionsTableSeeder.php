<?php

namespace App\Modules\Messages\Database\Seeds;

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
                'name'  => 'View the Messages List',
                'slug'  => 'module.messages.lists',
                'group' => 'messages',

                'roles' => array(1, 2, 3, 4),
            ),
            array(
                'name'  => 'Send Messages to other Users',
                'slug'  => 'module.messages.send',
                'group' => 'messages',

                'roles' => array(1, 2, 3, 4),
            ),
            array(
                'name'  => 'Receive Messages from other Users',
                'slug'  => 'module.messages.receive',
                'group' => 'messages',

                'roles' => array(1, 2, 3, 4),
            ),
        );

        $this->installPermissions($permissions);
    }
}
