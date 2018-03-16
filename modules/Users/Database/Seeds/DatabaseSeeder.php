<?php

namespace Modules\Users\Database\Seeds;

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
        Model::unguard();

        // Disable the Foreign Key Checks.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Call the seeders.
        $this->call('Modules\Users\Database\Seeds\UsersTableSeeder');
        $this->call('Modules\Users\Database\Seeds\FieldItemsTableSeeder');
        $this->call('Modules\Users\Database\Seeds\FieldsTableSeeder');
        $this->call('Modules\Users\Database\Seeds\PermissionsTableSeeder');

        // Enable the Foreign Key Checks.
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
