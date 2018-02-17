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

// The One-Time Authentication.
Route::get( 'authorize', array('middleware' => 'guest', 'uses' => 'Authorize@tokenRequest'));
Route::post('authorize', array('middleware' => 'guest', 'uses' => 'Authorize@tokenProcess'));

Route::get('authorize/{hash}/{time}/{token}', array(
    'middleware' => 'guest',

    'uses'  => 'Authorize@tokenLogin',
    'where' => array(
        'time' => '(\d+)'
    ),
));

// The Password Remind.
Route::get( 'password/remind', array('middleware' => 'guest', 'uses' => 'Reminders@remind'));
Route::post('password/remind', array('middleware' => 'guest', 'uses' => 'Reminders@postRemind'));

// The Password Reset.
Route::post('password/reset', array('middleware' => 'guest', 'uses' => 'Reminders@postReset'));

Route::get('password/reset/{hash}/{time}/{token}', array(
    'middleware' => 'guest',

    'uses'  => 'Reminders@reset',
    'where' => array(
        'time' => '(\d+)'
    ),
));

// The Account Registration.
Route::get( 'register',         array('middleware' => 'guest', 'uses' => 'Registrar@create'));
Route::post('register',         array('middleware' => 'guest', 'uses' => 'Registrar@store'));
Route::post('register/status',  array('middleware' => 'guest', 'uses' => 'Registrar@status'));
Route::get( 'register/verify',  array('middleware' => 'guest', 'uses' => 'Registrar@verify'));
Route::post('register/verify',  array('middleware' => 'guest', 'uses' => 'Registrar@verifyPost'));

Route::get('register/{hash}/{token?}', array(
    'middleware' => 'guest',

    'uses'  => 'Registrar@tokenVerify',
    'where' => array(
        'time' => '(\d+)'
    ),
));

// The User's Dashboard.
Route::get('dashboard', array('middleware' => 'auth', 'uses' => 'Dashboard@index'));

Route::get('dashboard/notify', array('middleware' => 'auth', 'uses' => 'Dashboard@notify'));

// The User's Account.
Route::get( 'account', array('middleware' => 'auth', 'uses' => 'Account@index'));
Route::post('account', array('middleware' => 'auth', 'uses' => 'Account@update'));

// The User's Notifications.
Route::get( 'notifications', array('middleware' => 'auth', 'uses' => 'Notifications@index'));
Route::post('notifications', array('middleware' => 'auth', 'uses' => 'Notifications@update'));

// The Heartbeat
Route::post('heartbeat', array('middleware' => 'auth', 'uses' => 'Heartbeat@update'));


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
