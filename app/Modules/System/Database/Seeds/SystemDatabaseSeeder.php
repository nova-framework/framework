<?php

namespace App\Modules\System\Database\Seeds;

use Nova\Database\Seeder;
use Nova\Database\ORM\Model;


class SystemDatabaseSeeder extends Seeder
{
    /**
     * Run the Database Seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call('App\Modules\System\Database\Seeds\FoobarTableSeeder');
        $this->call('App\Modules\System\Database\Seeds\RolesTableSeeder');
        $this->call('App\Modules\System\Database\Seeds\UsersTableSeeder');
    }
}
