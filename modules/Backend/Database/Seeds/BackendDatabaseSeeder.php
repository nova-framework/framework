<?php

namespace Modules\Backend\Database\Seeds;

use Nova\Database\Seeder;
use Nova\Database\ORM\Model;


class BackendDatabaseSeeder extends Seeder
{
    /**
     * Run the Database Seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call('Modules\Backend\Database\Seeds\FoobarTableSeeder');
    }
}
