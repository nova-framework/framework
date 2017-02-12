<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define static routes. */

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Modules\Logs\Http\Controllers\Admin'), function()
{
    // The Site Logs.
    Route::get( 'logs/{group?}', array('before' => 'auth',      'uses' => 'Logs@index'));
    Route::post('logs/clear',    array('before' => 'auth|csrf', 'uses' => 'Logs@clear'));
});
