<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define static routes. */

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'App\Modules\Dashboard\Controllers\Admin'), function()
{
    // The User's Dashboard.
    Route::get('/',         array('before' => 'auth', 'uses' => 'Dashboard@index'));
    Route::get('dashboard', array('before' => 'auth', 'uses' => 'Dashboard@index'));
});
