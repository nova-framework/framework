<?php

namespace Modules\Messages\Database\Seeds;

use Nova\Database\Seeder;
use Nova\Database\ORM\Model;


class DashboardDatabaseSeeder extends Seeder
{
    /**
     * Run the Database Seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call('Modules\Messages\Database\Seeds\FoobarTableSeeder');
    }
}
