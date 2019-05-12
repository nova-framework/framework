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
Route::get( 'login',  array('middleware' => 'guest', 'uses' => 'Authorize@index'));
Route::post('login',  array('middleware' => 'guest', 'uses' => 'Authorize@process'));
Route::post('logout', array('middleware' => 'auth',  'uses' => 'Authorize@logout'));

// The Public Area Routes.
Route::group(array('middleware' => 'guest'), function ()
{
    // The One-Time Authentication.
    Route::get( 'authorize', 'TokenLogins@index');
    Route::post('authorize', 'TokenLogins@process');

    Route::get('authorize/{hash}/{time}/{token}', 'TokenLogins@login')->where('time', '\d+');

    // The Password Reminder.
    Route::get( 'password/remind', 'PasswordReminders@remind');
    Route::post('password/remind', 'PasswordReminders@postRemind');

    // The Password Reset.
    Route::post('password/reset', 'PasswordReminders@postReset');

    Route::get('password/reset/{hash}/{time}/{token}', 'PasswordReminders@reset')->where('time', '\d+');

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
    // The User's Dashboard - for the On-line Users Widget we will use pagination.
    Route::paginate('dashboard', 'Dashboard@index');

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
