<?php

/*
|--------------------------------------------------------------------------
| Module Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for the module.
| It's a breeze. Simply tell Nova the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/** Define static routes. */

// The Frontend Area Routes.
Route::group(array('middleware' => 'auth'), function ()
{
    // The User's Dashboard - for the On-line Users Widget we will use pagination.
    Route::paginate('dashboard', 'Dashboard@index');

    Route::get('dashboard/notify', 'Dashboard@notify');

    // The User's Notifications.
    Route::get( 'notifications', 'Notifications@index');
    Route::post('notifications', 'Notifications@update');

    // The Heartbeat
    Route::post('heartbeat', 'Heartbeat@update');
});


// The Adminstration Routes.
Route::get('admin', function ()
{
    return Redirect::to('admin/dashboard');
});

Route::group(array('prefix' => 'admin', 'middleware' => 'auth', 'namespace' => 'Admin'), function ()
{
    // The Administration Dashboard - for the On-line Users Widget we will use pagination.
    Route::paginate('dashboard', 'Dashboard@index');

    // The Site Settings.
    Route::get( 'settings', 'Settings@index');
    Route::post('settings', 'Settings@store');
});
