<?php

/*
|--------------------------------------------------------------------------
| Module Bootstrap
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Bootstrap for the module.
*/

use App\Modules\Content\Models\Post;


/**
 * Register the Widgets.
 */
Widget::register('App\Modules\Content\Widgets\MainMenu', 'mainMenu');
