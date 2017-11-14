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
Widget::register('App\Modules\Content\Widgets\MainMenu', 'mainMenu');

Widget::register('App\Modules\Content\Widgets\Archives',   'contentArchives',   'content.posts.sidebar', 1);
Widget::register('App\Modules\Content\Widgets\Categories', 'contentCategories', 'content.posts.sidebar', 2);

