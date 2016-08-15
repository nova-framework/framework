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
Router::get('', 'App\Controllers\Welcome@index');
Router::get('subpage', 'App\Controllers\Welcome@subPage');

// Demo Routes
Router::get('demo/database',            'App\Controllers\Demo@database');

//Router::get('demo/password/(:any)',     'App\Controllers\Demo@password');
Router::get('demo/password/{password}', 'App\Controllers\Demo@password');

Router::get('demo/events',              'App\Controllers\Demo@events');
Router::get('demo/mailer',              'App\Controllers\Demo@mailer');
Router::get('demo/session',             'App\Controllers\Demo@session');
Router::get('demo/validate',            'App\Controllers\Demo@validate');
Router::get('demo/paginate',            'App\Controllers\Demo@paginate');
Router::get('demo/cache',               'App\Controllers\Demo@cache');

//Router::get('demo/request(/(:any)(/(:any)(/(:all))))', 'App\Controllers\Demo@request');
Router::get('demo/request/{param1?}/{param2?}/{slug?}', 'App\Controllers\Demo@request')->where('slug', '.*');

/*
Router::get('demo/test(/(:any)(/(:any)(/(:any)(/(:all)))))', array(
    'before' => 'test',
    'uses'   => 'App\Controllers\Demo@test'
));
*/
Router::get('demo/test/{param1?}/{param2?}/{param3?}/{slug?}', array(
    'before' => 'test',
    'uses'   => 'App\Controllers\Demo@test'
))->where('slug', '.*');

// The Framework's Language Changer.
//Router::get('language/(:any)', array(
Router::get('language/{code}', array(
    'before' => 'referer',
    'uses'   => 'App\Controllers\Language@change'
));

/** End default Routes */

/** Module Routes. */
$hooks = Hooks::get();

$hooks->run('routes');
/** End Module Routes. */

