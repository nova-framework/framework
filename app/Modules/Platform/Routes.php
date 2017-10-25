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

// The Password Remind.
Route::get( 'password/remind', array('middleware' => 'guest', 'uses' => 'Authorize@remind'));
Route::post('password/remind', array('middleware' => 'guest', 'uses' => 'Authorize@postRemind'));

// The Password Reset.
Route::get( 'password/reset/{token}', array('middleware' => 'guest', 'uses' => 'Authorize@reset'));
Route::post('password/reset',         array('middleware' => 'guest', 'uses' => 'Authorize@postReset'));

// The User's Dashboard.
Route::get('dashboard', array('middleware' => 'auth', 'uses' => 'Dashboard@index'));

Route::get('dashboard/notify', array('middleware' => 'auth', 'uses' => 'Dashboard@notify'));

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
