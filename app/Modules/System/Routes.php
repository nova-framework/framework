<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The alternate Framework's Language Changer.
//Route::get('language/{code}', array('before' => 'referer', 'uses' => 'App\Modules\System\Controllers\Language@change'));

// The CRON Route.
Route::get('cron/{token}', 'App\Modules\System\Controllers\Cron@run');

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'App\Modules\System\Controllers\Admin'), function() {
    Route::get( 'settings', array('before' => 'auth',      'uses' => 'Settings@index'));
    Route::post('settings', array('before' => 'auth|csrf', 'uses' => 'Settings@store'));
});
