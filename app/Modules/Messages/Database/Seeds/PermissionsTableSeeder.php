<?php

namespace App\Modules\Messages\Database\Seeds;

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
            array(
                'name'  => 'View the Messages List',
                'slug'  => 'module.messages.lists',
                'group' => 'messages',

                'roles' => array(1, 2, 3, 4),
            ),
            array(
                'name'  => 'View the Messages',
                'slug'  => 'module.messages.view',
                'group' => 'messages',

                'roles' => array(1, 2, 3, 4),
            ),
            array(
                'name'  => 'Create new Messages',
                'slug'  => 'module.messages.create',
                'group' => 'messages',

                'roles' => array(1, 2, 3, 4),
            ),
        );

        $this->createPermissions($permissions);
    }
}
