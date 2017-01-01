<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define static routes. */

Route::group(array('prefix' => '', 'namespace' => 'App\Modules\System\Controllers'), function()
{
    // The Framework's Language Changer.
    Route::get('language/{code}', array('before' => 'referer', 'uses' => 'Language@change'));

    // The CRON runner.
    Route::get('cron/{token}', array('uses' => 'CronRunner@index'));
});

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'App\Modules\System\Controllers\Admin'), function()
{
    // The Site Settings.
    Route::get( 'settings', array('before' => 'auth',      'uses' => 'Settings@index'));
    Route::post('settings', array('before' => 'auth|csrf', 'uses' => 'Settings@store'));

    // The Site Logs.
    Route::get( 'logs/{group?}', array('before' => 'auth',      'uses' => 'Logs@index'));
    Route::post('logs/clear',    array('before' => 'auth|csrf', 'uses' => 'Logs@clear'));
});
