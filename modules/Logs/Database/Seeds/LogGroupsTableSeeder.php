<?php

namespace Logs\Database\Seeds;

use Nova\Database\Seeder;
use Nova\Database\ORM\Model;

use Logs\Models\LogGroup;


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
            'name'        => __d('logs', 'Generic'),
            'slug'        => 'generic',
            'description' => 'This is the group which used by default on logging.',
        ));

        LogGroup::create(array(
            'id'          => 2,
            'name'        => __d('logs', 'System'),
            'slug'        => 'system',
            'description' => 'This is the group which used for logging of the System actions.',
        ));

        LogGroup::create(array(
            'id'          => 3,
            'name'        => __d('logs', 'Database'),
            'slug'        => 'database',
            'description' => 'This is the group which used for logging of the ORM actions.',
        ));

        LogGroup::create(array(
            'id'          => 4,
            'name'        => __d('logs', 'Auth'),
            'slug'        => 'auth',
            'description' => 'This is the group which used for logging of the Auth System actions.',
        ));
    }
}
