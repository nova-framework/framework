<?php
/**
 * Routes - all standard routes are defined here.
 *
 * @author David Carr - dave@daveismyname.com
 * @version 3.0
 */

use Core\Router;
use Helpers\Hooks;


/** Define static routes. */

// Default Routing
Router::any('', 'App\Controllers\Welcome@index');
Router::any('subpage', 'App\Controllers\Welcome@subPage');

Router::any('admin/(:any)(/(:any)(/(:any)(/(:all))))', array(
    'filters' => 'test',
    'uses'    => 'App\Controllers\Demo@test'
));

// The default Auth Routes.
Router::any('login',  array('filters' => 'guest|csrf', 'uses' => 'App\Controllers\Users@login'));
Router::get('logout', array('filters' => 'auth',       'uses' => 'App\Controllers\Users@logout'));

// The User's Dashboard.
Router::get('dashboard', array('filters' => 'auth', 'uses' => 'App\Controllers\Users@dashboard'));

// The User's Profile.
Router::any('profile', array('filters' => 'auth|csrf', 'uses' => 'App\Controllers\Users@profile'));

// The Framework's Language Changer.
Router::any('language/(:any)', 'App\Controllers\Language@change');
/** End default Routes */

/** Module Routes. */
$hooks = Hooks::get();

$hooks->run('routes');
/** End Module Routes. */

