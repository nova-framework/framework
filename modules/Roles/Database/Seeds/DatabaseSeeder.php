<?php

namespace Modules\Roles\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;


class DatabaseSeeder extends Seeder
{

    /**
     * Run the Database Seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // 
        $this->call('Modules\Roles\Database\Seeds\RolesTableSeeder');
        $this->call('Modules\Roles\Database\Seeds\PermissionsTableSeeder');
    }
}
