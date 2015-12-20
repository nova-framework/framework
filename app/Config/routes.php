<?php
/**
 * Routes - all standard routes are defined here.
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date updated Sept 19, 2015
 */

/** Create alias for Router. */
use Nova\Net\Router;
use Nova\Helpers\Hooks;

/** Define static routes. */

// Default Routing
Router::any('', 'App\Controllers\Welcome@index');
Router::any('subpage', 'App\Controllers\Welcome@subPage');
Router::any('admin/(:any)(/(:all))', 'App\Controllers\Demo@test');

Router::any('database', 'App\Controllers\Demo@database');
Router::any('database/insert', 'App\Controllers\Demo@databaseInsert');
Router::any('database/sqlite', 'App\Controllers\Demo@databaseSqlite');

Router::any('demos/themed/welcome', 'App\Controllers\Demos\Themed@welcome');
Router::any('demos/themed/subpage', 'App\Controllers\Demos\Themed@subPage');

Router::any('demos/classic/welcome', 'App\Controllers\Demos\Classic@welcome');
Router::any('demos/classic/subpage', 'App\Controllers\Demos\Classic@subPage');

Router::any('demos/events', 'App\Controllers\Demos\Events@index');

Router::any('(:all)', 'App\Controllers\Demo@catchAll');
/*
// Classic Routing
Router::any('', 'welcome/index');
Router::any('subpage', 'welcome/subpage');
Router::any('admin/(:any)/(:all)', 'demo/test/$1/$2');
Router::any('database', 'demo/database');
Router::any('(:all)', 'demo/catchall/$1');
*/
/** End static routes */

/** Module routes. */
$hooks = Hooks::get();
$hooks->run('routes');
/** End Module routes. */

/** If no route found. */
Router::error('\App\Controllers\Error@error404');

