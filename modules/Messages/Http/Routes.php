<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define static routes. */

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Modules\Messages\Http\Controllers\Admin'), function()
{
    // Messages
    Route::get( 'messages',                  array('middleware' => 'auth', 'uses' => 'Messages@index'));
    Route::get( 'messages/create',           array('middleware' => 'auth', 'uses' => 'Messages@create'));
    Route::post('messages',                  array('middleware' => 'auth', 'uses' => 'Messages@store'));
    Route::get( 'messages/{threadId}',       array('middleware' => 'auth', 'uses' => 'Messages@show'));
    //Route::post('messages/{postId}/destroy', array('middleware' => 'auth', 'uses' => 'Messages@destroy'));

    Route::post('messages/{threadId}', array('middleware' => 'auth', 'uses' => 'Messages@reply'));
});
