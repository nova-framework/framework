<?php
/**
 * Routes - all standard routes are defined here.
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date updated Sept 19, 2015
 */

/** Create alias for Router. */
use Core\Router;
use Helpers\Hooks;

/** Get the Router instance. */
$router = Router::getInstance();

/** Define static routes. */
#Router::any('', 'App\Controllers\Welcome@index');
#Router::any('subpage', 'App\Controllers\Welcome@subPage');

// Classic Routing
Router::any('', '/welcome/index');
Router::any('subpage', '/welcome/subpage');

/** End static routes */

/** Module routes. */
$hooks = Hooks::get();
$hooks->run('routes');
/** End Module routes. */

/** If no route found. */
Router::error('Core\Error@index');

/** Execute matched routes. */
$router->dispatch();
