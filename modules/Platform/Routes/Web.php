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

// The default Auth Routes.
Route::get( 'login',  array('middleware' => 'guest', 'uses' => 'Authorize@login'));
Route::post('login',  array('middleware' => 'guest', 'uses' => 'Authorize@postLogin'));
Route::post('logout', array('middleware' => 'auth',  'uses' => 'Authorize@logout'));

// The Public Area Routes.
Route::group(array('middleware' => 'guest'), function ()
{
    // The One-Time Authentication.
    Route::get( 'authorize', 'Authorize@tokenRequest');
    Route::post('authorize', 'Authorize@tokenProcess');

    Route::get('authorize/{hash}/{time}/{token}', 'Authorize@tokenLogin')->where('time', '\d+');

    // The Password Remind.
    Route::get( 'password/remind', 'Reminders@remind');
    Route::post('password/remind', 'Reminders@postRemind');

    // The Password Reset.
    Route::post('password/reset', 'Reminders@postReset');

    Route::get('password/reset/{hash}/{time}/{token}', 'Reminders@reset')->where('time', '\d+');

    // The Account Registration.
    Route::get( 'register',        'Registrar@create');
    Route::post('register',        'Registrar@store');
    Route::get( 'register/status', 'Registrar@status');
    Route::get( 'register/verify', 'Registrar@verify');
    Route::post('register/verify', 'Registrar@verifyPost');

    Route::get('register/{hash}/{token?}', 'Registrar@tokenVerify');
});

// The Frontend Area Routes.
Route::group(array('middleware' => 'auth'), function ()
{
    // The User's Dashboard.
    Route::get('dashboard', 'Dashboard@index');

    Route::get('dashboard/notify', 'Dashboard@notify');

    // The User's Account.
    Route::get( 'account',         'Account@index');
    Route::post('account',         'Account@update');
    Route::post('account/picture', 'Account@picture');

    // The User's Notifications.
    Route::get( 'notifications', 'Notifications@index');
    Route::post('notifications', 'Notifications@update');

    // The Heartbeat
    Route::post('heartbeat', 'Heartbeat@update');
});

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'middleware' => 'auth', 'namespace' => 'Admin'), function ()
{
    // The Administration Dashboard.
    Route::get('/',         'Dashboard@index');
    Route::get('dashboard', 'Dashboard@index');

    // The Site Settings.
    Route::get( 'settings', 'Settings@index');
    Route::post('settings', 'Settings@store');
});
