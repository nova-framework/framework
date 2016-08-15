<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The alternate Framework's Language Changer.
/*
Route::any('language/{code}', array(
    'before' => 'referer',
    'uses'   => 'App\Modules\Settings\Controllers\Language@change'
));
*/

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'App\Modules\Settings\Controllers\Admin'), function() {
    Route::get( 'settings', array('before' => 'auth',      'uses' => 'Settings@index'));
    Route::post('settings', array('before' => 'auth|csrf', 'uses' => 'Settings@store'));
});

