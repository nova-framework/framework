<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The Demo Routes
Route::group(array('prefix' => 'demo'), function()
{
    Route::get('database',   'Demos@database');
    Route::get('events',     'Demos@events');
    Route::get('mailer',     'Demos@mailer');
    Route::get('session',    'Demos@session');
    Route::get('validation', 'Demos@validation');
    Route::get('pagination', 'Demos@pagination');
    Route::get('cache',      'Demos@cache');
    Route::get('modules',    'Demos@modules');

    Route::get('password/{password}', 'Demos@password');

    //
    Route::get('request/{param1?}/{param2?}/{slug?}', 'Demos@request')
        ->where('slug', '(.*)');

    Route::get('test/{param1?}/{param2?}/{param3?}/{slug?}', array('before' => 'test', 'uses' => 'Demos@test'))
        ->where('slug', '(.*)');
});


// A catch-all Route - will match any URI, while using any HTTP Method.
//Route::any('{slug}', 'Demo@catchAll')->where('slug', '(.*)');
