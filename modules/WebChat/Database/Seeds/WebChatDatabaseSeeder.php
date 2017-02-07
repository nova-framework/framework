<?php

namespace Modules\WebChat\Database\Seeds;

use Nova\Database\Seeder;
use Nova\Database\ORM\Model;


class WebChatDatabaseSeeder extends Seeder
{
    /**
     * Run the Database Seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call('Modules\WebChat\Database\Seeds\FoobarTableSeeder');
    }
}
