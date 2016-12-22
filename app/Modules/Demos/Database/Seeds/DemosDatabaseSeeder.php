<?php

namespace App\Modules\Demos\Database\Seeds;

use Nova\Database\Seeder;
use Nova\Database\ORM\Model;


class DemosDatabaseSeeder extends Seeder
{
    /**
     * Run the Database Seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call('App\Modules\Demos\Database\Seeds\FoobarTableSeeder');
    }
}
