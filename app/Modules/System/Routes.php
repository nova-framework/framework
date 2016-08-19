<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

Route::group(array('prefix' => '', 'namespace' => 'App\Modules\System\Controllers'), function() {
    // The Framework's Language Changer.
    Route::get('language/{code}', array('before' => 'referer', 'uses' => 'Language@change'));

    // The CRON runner.
    Route::get('cron/{token}', array('uses' => 'CronRunner@index'));

    // The default Auth Routes.
    Route::get( 'login',  array('before' => 'guest',      'uses' => 'Authorize@login'));
    Route::post('login',  array('before' => 'guest|csrf', 'uses' => 'Authorize@postLogin'));
    Route::get( 'logout', array('before' => 'auth',       'uses' => 'Authorize@logout'));

    // The Password Remind.
    Route::get( 'password/remind', array('before' => 'guest',      'uses' => 'Authorize@remind'));
    Route::post('password/remind', array('before' => 'guest|csrf', 'uses' => 'Authorize@postRemind'));

    // The Password Reset.
    Route::get( 'password/reset/{token?}', array('before' => 'guest',      'uses' => 'Authorize@reset'));
    Route::post('password/reset',          array('before' => 'guest|csrf', 'uses' => 'Authorize@postReset'));
});

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'App\Modules\System\Controllers\Admin'), function() {
    // The User's Dashboard.
    Route::get('/',         array('before' => 'auth', 'uses' => 'Dashboard@index'));
    Route::get('dashboard', array('before' => 'auth', 'uses' => 'Dashboard@index'));

    // The User's Profile.
    Route::get( 'profile', array('before' => 'auth',      'uses' => 'Profile@index'));
    Route::post('profile', array('before' => 'auth|csrf', 'uses' => 'Profile@store'));

    // The Site Settings.
    Route::get( 'settings', array('before' => 'auth',      'uses' => 'Settings@index'));
    Route::post('settings', array('before' => 'auth|csrf', 'uses' => 'Settings@store'));
});
