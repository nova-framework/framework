<?php

/*
|--------------------------------------------------------------------------
| Module Events
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Events for the module.
*/

use App\Models\Option;


Event::listen('backend.menu.sidebar', function ()
{
    return array(
        array(
            'url'    => '#',
            'title'  => __d('settings', 'Platform'),
            'icon'   => 'cube',
            'weight' => 0,

            //
            'path'   => 'platform',
        ),
        array(
            'url'    => site_url('admin/settings'),
            'title'  => __d('settings', 'Settings'),
            'icon'   => 'circle-o',
            'weight' => 0,

            //
            'path'   => 'platform.settings',
            'can'    => 'manage:' .Option::class,
        ),
    );
});
