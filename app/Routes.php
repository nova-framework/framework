<?php
/**
 * Routes - all standard Routes are defined here.
 *
 * @author David Carr - dave@daveismyname.com
 * @version 3.0
 */

use Helpers\Hooks;


/** Define static routes. */

// Default Routing
Router::any('', 'App\Controllers\Welcome@index');
Router::any('subpage', 'App\Controllers\Welcome@subPage');

// Demo Routes
Router::any('demo/database',            'App\Controllers\Demo@database');

//Router::any('demo/password/(:any)',     'App\Controllers\Demo@password');
Router::any('demo/password/{password}', 'App\Controllers\Demo@password');

Router::any('demo/events',              'App\Controllers\Demo@events');
Router::any('demo/mailer',              'App\Controllers\Demo@mailer');
Router::any('demo/session',             'App\Controllers\Demo@session');
Router::any('demo/validate',            'App\Controllers\Demo@validate');
Router::any('demo/paginate',            'App\Controllers\Demo@paginate');
Router::any('demo/cache',               'App\Controllers\Demo@cache');

//Router::any('demo/request(/(:any)(/(:any)(/(:all))))', 'App\Controllers\Demo@request');
Router::any('demo/request/{param1?}/{param2?}/{slug:.*:?}', 'App\Controllers\Demo@request');

//Router::any('demo/test(/(:any)(/(:any)(/(:any)(/(:all)))))',, array(
Router::any('demo/test/{param1?}/{param2?}/{param3?}/{slug:.*:?}', array(
    'before' => 'test',
    'uses'   => 'App\Controllers\Demo@test'
));

// The Framework's Language Changer.
//Router::any('language/(:any)', array(
Router::any('language/{code}', array(
    'before' => 'referer',
    'uses'   => 'App\Controllers\Language@change'
));

/** End default Routes */

/** Module Routes. */
$hooks = Hooks::get();

$hooks->run('routes');
/** End Module Routes. */

