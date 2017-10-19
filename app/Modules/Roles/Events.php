<?php

/*
|--------------------------------------------------------------------------
| Module Events
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Events for the module.
*/

use App\Modules\Roles\Models\Role;


/** Define Events. */

Event::listen('backend.menu.sidebar', function ()
{
    return array(
        array(
            'url'    => '#',
            'title'  => __d('roles', 'Platform'),
            'icon'   => 'cube',
            'weight' => 0,

            //
            'path'   => 'platform',
        ),
        array(
            'url'    => site_url('admin/roles'),
            'title'  => __d('roles', 'User Roles'),
            'icon'   => 'circle-o',
            'weight' => 1,

            //
            'path'   => 'platform.roles',
            'can'    => 'lists:' .Role::class,
        ),
    );
});

