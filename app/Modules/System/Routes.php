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
    Route::get('cron/{token}', array('uses' => 'Cron@run'));
});

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'App\Modules\System\Controllers\Admin'), function() {
    Route::get( 'settings', array('before' => 'auth',      'uses' => 'Settings@index'));
    Route::post('settings', array('before' => 'auth|csrf', 'uses' => 'Settings@store'));
});
