<?php

namespace App\Modules\System\Database\Seeds;

use Nova\Database\Seeder;
use Nova\Database\ORM\Model;

use App\Modules\System\Models\LogGroup;


class LogGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LogGroup::create(array(
            'id'          => 1,
            'name'        => 'Generic',
            'slug'        => 'generic',
            'description' => 'This is the group which used by default on logging.',
        ));

        LogGroup::create(array(
            'id'          => 2,
            'name'        => 'Database',
            'slug'        => 'database',
            'description' => 'This is the group which used for logging of the ORM actions.',
        ));

        LogGroup::create(array(
            'id'          => 3,
            'name'        => 'Auth',
            'slug'        => 'auth',
            'description' => 'This is the group which used for logging of the Auth System actions.',
        ));
    }
}
