<?php

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;


class DatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        //
        $this->call('App\Database\Seeds\RolesTableSeeder');
        $this->call('App\Database\Seeds\PermissionsTableSeeder');
        $this->call('App\Database\Seeds\UsersTableSeeder');
    }
}
