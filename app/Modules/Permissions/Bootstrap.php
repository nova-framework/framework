<?php

/*
|--------------------------------------------------------------------------
| Module Bootstrap
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Bootstrap for the module.
*/


/**
 * Register the Widgets.
 */
Widget::register('App\Modules\Permissions\Widgets\RegisteredPermissions', 'registeredPermissions', 'backend.dashboard.top', 2);
