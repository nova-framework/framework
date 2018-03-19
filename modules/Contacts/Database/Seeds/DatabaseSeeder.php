<?php

namespace Modules\Contacts\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;
use Nova\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{

    /**
     * Run the Database Seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Model::unguard();

        //
        $this->call('Modules\Contacts\Database\Seeds\ContactsTableSeeder');
        $this->call('Modules\Contacts\Database\Seeds\FieldGroupsTableSeeder');
        $this->call('Modules\Contacts\Database\Seeds\FieldItemsTableSeeder');

        $this->call('Modules\Contacts\Database\Seeds\PostsTableSeeder');
        $this->call('Modules\Contacts\Database\Seeds\PermissionsTableSeeder');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
