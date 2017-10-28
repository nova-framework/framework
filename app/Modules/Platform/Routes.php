<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The default Auth Routes.
Route::get( 'login',  array('middleware' => 'guest', 'uses' => 'Authorize@login'));
Route::post('login',  array('middleware' => 'guest', 'uses' => 'Authorize@postLogin'));
Route::post('logout', array('middleware' => 'auth',  'uses' => 'Authorize@logout'));

// The One-Time Authentication.
Route::get( 'authorize',                array('middleware' => 'guest', 'uses' => 'Authorize@tokenRequest'));
Route::post('authorize',                array('middleware' => 'guest', 'uses' => 'Authorize@tokenProcess'));
Route::get( 'authorize/{hash}/{token}', array('middleware' => 'guest', 'uses' => 'Authorize@tokenLogin'));

// The Password Remind.
Route::get( 'password/remind', array('middleware' => 'guest', 'uses' => 'Reminders@remind'));
Route::post('password/remind', array('middleware' => 'guest', 'uses' => 'Reminders@postRemind'));

// The Password Reset.
Route::get( 'password/reset/{token}', array('middleware' => 'guest', 'uses' => 'Reminders@reset'));
Route::post('password/reset',         array('middleware' => 'guest', 'uses' => 'Reminders@postReset'));

// The Account Registration.
Route::get( 'register',          array('before' => 'guest', 'uses' => 'Registrar@create'));
Route::post('register',          array('before' => 'guest', 'uses' => 'Registrar@store'));
Route::get( 'register/status',   array('before' => 'guest', 'uses' => 'Registrar@status'));
Route::get( 'register/{token?}', array('before' => 'guest', 'uses' => 'Registrar@verify'));

// The User's Dashboard.
Route::get('dashboard', array('middleware' => 'auth', 'uses' => 'Dashboard@index'));

Route::get('dashboard/notify', array('middleware' => 'auth', 'uses' => 'Dashboard@notify'));

// The User's Account.
Route::get( 'account',         array('middleware' => 'auth', 'uses' => 'Account@index'));
Route::post('account',         array('middleware' => 'auth', 'uses' => 'Account@update'));
Route::post('account/picture', array('middleware' => 'auth', 'uses' => 'Account@picture'));

// The Heartbeat
Route::post('heartbeat', array('middleware' => 'auth', 'uses' => 'Heartbeat@update'));

// The Notifications.
Route::get( 'notifications',      array('middleware' => 'auth', 'uses' => 'Notifications@index'));
Route::post('notifications',      array('middleware' => 'auth', 'uses' => 'Notifications@update'));


// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Admin'), function ()
{
    // The Administration Dashboard.
    Route::get('/',         array('middleware' => 'auth', 'uses' => 'Dashboard@index'));
    Route::get('dashboard', array('middleware' => 'auth', 'uses' => 'Dashboard@index'));

    // The Site Settings.
    Route::get( 'settings', array('middleware' => 'auth', 'uses' => 'Settings@index'));
    Route::post('settings', array('middleware' => 'auth', 'uses' => 'Settings@store'));
});
