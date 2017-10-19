<?php

namespace App\Modules\Files\Database\Seeds;

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

        $this->createPermissions($permissions);
    }
}
