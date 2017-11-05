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
Widget::register('App\Modules\Roles\Widgets\RegisteredRoles', 'registeredRoles', 'backend.dashboard.top', 1);
