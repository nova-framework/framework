<?php
/**
 * Routes - all Routes for the current Module are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 23th, 2015
 */

use Nova\Net\Router;


/** The Module's known Routes definition. */
Router::get('demos', 'App\Modules\Demo\Controllers\Index@home');

Router::any('demos/themed/welcome', 'App\Modules\Demo\Controllers\Themed@welcome');
Router::any('demos/themed/subpage', 'App\Modules\Demo\Controllers\Themed@subPage');

Router::any('demos/classic/welcome', 'App\Modules\Demo\Controllers\Classic@welcome');
Router::any('demos/classic/subpage', 'App\Modules\Demo\Controllers\Classic@subPage');

Router::any('demos/events', 'App\Modules\Demo\Controllers\Events@index');
