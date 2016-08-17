<?php
/**
 * Routes - all standard Routes are defined here.
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The default Routing
Route::get('/',       'App\Controllers\Welcome@index');
Route::get('subpage', 'App\Controllers\Welcome@subPage');

// The Demo Routes
Route::group(array('prefix' => 'demo', 'namespace' => 'App\Controllers'), function() {
    Route::get('database', 'Demo@database');
    Route::get('events',   'Demo@events');
    Route::get('mailer',   'Demo@mailer');
    Route::get('session',  'Demo@session');
    Route::get('validate', 'Demo@validate');
    Route::get('paginate', 'Demo@paginate');
    Route::get('cache',    'Demo@cache');

    //Route::get('password/(:any)',     'Demo@password');
    Route::get('password/{password}', 'Demo@password');

    //Route::get('request(/(:any)(/(:any)(/(:all))))', 'Demo@request');
    Route::get('request/{param1?}/{param2?}/{slug?}', 'Demo@request')
        ->where('slug', '(.*)');

    //Route::get('test(/(:any)(/(:any)(/(:any)(/(:all)))))', array('before' => 'test', 'uses' => 'Demo@test'));
    Route::get('test/{param1?}/{param2?}/{param3?}/{slug?}', array('before' => 'test', 'uses' => 'Demo@test'))
        ->where('slug', '(.*)');
});

//
// The catch-all Route - when enabled, it will capture any URI, with any HTTP Method.
// NOTE: ensure that it is the last one defined, otherwise it will mask other Routes.

//Route::any('(:all)', 'App\Controllers\Demo@catchAll');
Route::any('{slug}', 'App\Controllers\Demo@catchAll')->where('slug', '(.*)');

/** End default Routes */
