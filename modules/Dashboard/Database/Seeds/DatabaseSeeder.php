<?php

namespace Modules\Dashboard\Database\Seeds;

use Nova\Database\Seeder;
use Nova\Database\ORM\Model;


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

        // $this->call('Modules\Dashboard\Database\Seeds\FoobarTableSeeder');
    }
}
