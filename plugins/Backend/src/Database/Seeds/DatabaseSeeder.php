<?php

namespace AcmeCorp\Backend\Database\Seeds;

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
        //$this->call('AcmeCorp\Backend\Database\Seeds\FoobarTableSeeder');
        $this->call('AcmeCorp\Backend\Database\Seeds\RolesTableSeeder');
        $this->call('AcmeCorp\Backend\Database\Seeds\UsersTableSeeder');
    }

}
