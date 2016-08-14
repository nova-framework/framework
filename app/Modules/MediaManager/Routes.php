<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'App\Modules\MediaManager\Controllers\Admin'), function() {
    Route::any('files',                   array('before' => 'auth', 'uses' => 'Files@index'));
    Route::any('files/connector',         array('before' => 'auth', 'uses' => 'Files@connector'));
    Route::any('files/thumbnails/{path}', array('before' => 'auth', 'uses' => 'Files@thumbnails'));
    Route::any('files/preview',           array('before' => 'auth', 'uses' => 'Files@preview'));
});
