<?php

/*
|--------------------------------------------------------------------------
| Module Events
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Events for the module.
*/

use App\Modules\Permissions\Models\Permission;


Event::listen('backend.menu.sidebar', function ()
{
    return array(
        array(
            'url'    => '#',
            'title'  => __d('permissions', 'Platform'),
            'icon'   => 'cube',
            'weight' => 0,

            //
            'path'   => 'platform',
        ),
        array(
            'url'    => site_url('admin/permissions'),
            'title'  => __d('permissions', 'Permissions'),
            'icon'   => 'circle-o',
            'weight' => 1,

            //
            'path'   => 'platform.permissions',
            'can'    => 'manage:' .Permission::class,
        ),
    );
});
