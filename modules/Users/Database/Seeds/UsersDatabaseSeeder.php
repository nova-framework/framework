<?php

namespace Modules\Users\Database\Seeds;

use Nova\Database\Seeder;
use Nova\Database\ORM\Model;


class UsersDatabaseSeeder extends Seeder
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
        $this->call('Modules\Users\Database\Seeds\UsersTableSeeder');
        $this->call('Modules\Users\Database\Seeds\RolesTableSeeder');
    }
}
