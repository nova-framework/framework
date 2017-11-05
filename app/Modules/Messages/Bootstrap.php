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
Widget::register('App\Modules\Messages\Widgets\Messages', 'messages', 'backend.dashboard.top', 4);
