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

// The Account Registration.
Route::get( 'register',                 array('before' => 'guest',      'uses' => 'Registrar@create'));
Route::post('register',                 array('before' => 'guest|csrf', 'uses' => 'Registrar@store'));
Route::get( 'register/verify/{token?}', array('before' => 'guest',      'uses' => 'Registrar@verify'));
Route::get( 'register/status',          array('before' => 'guest',      'uses' => 'Registrar@status'));

// The User's Dashboard.
Route::get('dashboard', array('before' => 'auth', 'uses' => 'Dashboard@index'));

Route::get('dashboard/notify', array('before' => 'auth', 'uses' => 'Dashboard@notify'));

// The User's Account.
Route::get( 'account', array('before' => 'auth',      'uses' => 'Account@edit'));
Route::post('account', array('before' => 'auth|csrf', 'uses' => 'Account@update'));

// The Notifications.
Route::get( 'notifications/data', array('before' => 'auth', 'uses' => 'Notifications@data'));

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
