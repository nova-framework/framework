<?php

namespace App\Modules\Dashboard\Database\Seeds;

use Nova\Database\Seeder;
use Nova\Database\ORM\Model;


class VideoChatDatabaseSeeder extends Seeder
{
    /**
     * Run the Database Seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call('App\Modules\Dashboard\Database\Seeds\FoobarTableSeeder');
    }
}
