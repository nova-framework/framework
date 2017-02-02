<?php

namespace Files\Database\Seeds;

use Nova\Database\Seeder;
use Nova\Database\ORM\Model;


class FilesDatabaseSeeder extends Seeder
{
    /**
     * Run the Database Seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call('Files\Database\Seeds\FoobarTableSeeder');
    }
}
