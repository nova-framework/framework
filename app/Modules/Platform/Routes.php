<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The default Auth Routes.
Route::get( 'login',  array('before' => 'guest',      'uses' => 'Authorize@login'));
Route::post('login',  array('before' => 'guest|csrf', 'uses' => 'Authorize@postLogin'));
Route::post('logout', array('before' => 'auth|csrf',  'uses' => 'Authorize@logout'));

// The Password Remind.
Route::get( 'password/remind', array('before' => 'guest',      'uses' => 'Authorize@remind'));
Route::post('password/remind', array('before' => 'guest|csrf', 'uses' => 'Authorize@postRemind'));

// The Password Reset.
Route::get( 'password/reset/{token}', array('before' => 'guest',      'uses' => 'Authorize@reset'));
Route::post('password/reset',         array('before' => 'guest|csrf', 'uses' => 'Authorize@postReset'));

// The User's Dashboard.
Route::get('dashboard', array('before' => 'auth', 'uses' => 'Dashboard@index'));

Route::get('dashboard/notify', array('before' => 'auth', 'uses' => 'Dashboard@notify'));

// The Heartbeat
Route::post('heartbeat', array('before' => 'auth|csrf', 'uses' => 'Heartbeat@update'));

// The Notifications.
Route::get( 'notifications',      array('before' => 'auth',      'uses' => 'Notifications@index'));
Route::post('notifications',      array('before' => 'auth|csrf', 'uses' => 'Notifications@update'));


// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Admin'), function ()
{
    // The Administration Dashboard.
    Route::get('/',         array('before' => 'auth', 'uses' => 'Dashboard@index'));
    Route::get('dashboard', array('before' => 'auth', 'uses' => 'Dashboard@index'));

    // The Site Settings.
    Route::get( 'settings', array('before' => 'auth',      'uses' => 'Settings@index'));
    Route::post('settings', array('before' => 'auth|csrf', 'uses' => 'Settings@store'));
});
