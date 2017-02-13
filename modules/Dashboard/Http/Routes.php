<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define static routes. */

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Modules\Dashboard\Http\Controllers\Admin'), function()
{
    // The User's Dashboard.
    Route::get('/',         array('middleware' => 'auth', 'uses' => 'Dashboard@index'));
    Route::get('dashboard', array('middleware' => 'auth', 'uses' => 'Dashboard@index'));
});
