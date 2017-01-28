<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define static routes. */

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Modules\Messages\Controllers\Admin'), function()
{
    // Messages
    Route::get( 'messages',                  array('before' => 'auth',      'uses' => 'Messages@index'));
    Route::get( 'messages/create',           array('before' => 'auth',      'uses' => 'Messages@create'));
    Route::post('messages',                  array('before' => 'auth|csrf', 'uses' => 'Messages@store'));
    Route::get( 'messages/{threadId}',       array('before' => 'auth',      'uses' => 'Messages@show'));
    //Route::post('messages/{postId}/destroy', array('before' => 'auth|csrf', 'uses' => 'Messages@destroy'));

    Route::post('messages/{threadId}', array('before' => 'auth|csrf', 'uses' => 'Messages@reply'));
});
