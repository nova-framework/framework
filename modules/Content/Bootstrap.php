<?php

/*
|--------------------------------------------------------------------------
| Module Bootstrap
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Bootstrap for the module.
*/

use Modules\Content\Models\Post;


/**
 * Register the Widgets.
 */
Widget::register('Modules\Content\Widgets\MainMenu', 'mainMenu');
