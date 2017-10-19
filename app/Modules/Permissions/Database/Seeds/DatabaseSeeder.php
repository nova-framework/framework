<?php

namespace App\Modules\Permissions\Database\Seeds;

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
        $this->call('App\Modules\Permissions\Database\Seeds\PermissionsTableSeeder');
    }
}
