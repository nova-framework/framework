<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Admin'), function ()
{
    Route::get('files', array('middleware' => 'auth', 'uses' => 'Files@index'));

    Route::match(array('GET', 'POST'), 'files/connector', array('middleware' => 'auth', 'uses' => 'Files@connector'));

    // Thumbnails Files serving.
    Route::get('files/thumbnails/{file}', array('middleware' => 'auth', 'uses' => 'Files@thumbnails'));

    // Preview Files serving.
    Route::get('files/preview/{path}', array('middleware' => 'auth', 'uses' => 'Files@preview'))->where('path', '(.*)');
});
