<?php
/**
 * Routes - all standard routes are defined here.
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date updated Sept 19, 2015
 */

/** Create alias for Router. */
use Smvc\Core\Router;
use Smvc\Helpers\Hooks;

/** Get the Router instance. */
$router = Router::getInstance();

/** Define static routes. */

// Default Routing
Router::any('', 'App\Controllers\Welcome@index');
Router::any('subpage', 'App\Controllers\Welcome@subPage');
Router::any('admin/(:any)(/(:all))', 'App\Controllers\Demo@test');
Router::any('database', 'App\Controllers\Demo@database');
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
Router::error('\Smvc\Core\Error@index');

/** Execute matched routes. */
$router->dispatch();
