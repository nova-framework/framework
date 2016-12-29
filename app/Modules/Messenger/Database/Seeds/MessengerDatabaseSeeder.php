<?php

namespace App\Modules\Messenger\Database\Seeds;

use Nova\Database\Seeder;
use Nova\Database\ORM\Model;


class MessengerDatabaseSeeder extends Seeder
{
    /**
     * Run the Database Seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call('App\Modules\Messenger\Database\Seeds\FoobarTableSeeder');
    }
}
