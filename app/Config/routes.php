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

/** Define static routes. */

// Default Routing
Router::any('', 'App\Controllers\Welcome@index');
Router::any('subpage', 'App\Controllers\Welcome@subPage');
Router::any('admin/(:any)(/(:all))', 'App\Controllers\Demo@test');

Router::any('database', 'App\Controllers\Demo@database');
Router::any('database/insert', 'App\Controllers\Demo@databaseInsert');
Router::any('database/sqlite', 'App\Controllers\Demo@databaseSqlite');

// WARNING! The following Route catch all the Requests!
// That's why it should be defined last and/or in the last Module.
//Router::any('(:all)', 'App\Controllers\Demo@catchAll');

/*
// Classic Routing
Router::any('', 'welcome/index');
Router::any('subpage', 'welcome/subpage');
Router::any('admin/(:any)/(:all)', 'demo/test/$1/$2');
Router::any('database', 'demo/database');
Router::any('(:all)', 'demo/catchall/$1');
*/
/** End static routes */

/** If no route found. */
Router::error('\App\Controllers\Error@error404');

