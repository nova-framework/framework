<?php

namespace Modules\Contacts\Database\Seeds;

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
        $this->call('Modules\Contacts\Database\Seeds\ContactsTableSeeder');
        $this->call('Modules\Contacts\Database\Seeds\PostsTableSeeder');
        $this->call('Modules\Contacts\Database\Seeds\PermissionsTableSeeder');
    }
}
