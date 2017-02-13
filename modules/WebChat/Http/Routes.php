<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define static routes. */

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Modules\WebChat\Http\Controllers\Admin'), function()
{
    // Chat
    Route::get( 'chat', array('middleware' => 'auth', 'uses' => 'Chat@index'));

    // Video Chat
    Route::get( 'chat/video',          array('middleware' => 'auth', 'uses' => 'VideoChat@index'));
    Route::post('chat/video',          array('middleware' => 'auth', 'uses' => 'VideoChat@create'));
    Route::get( 'chat/video/{roomId}', array('middleware' => 'auth', 'uses' => 'VideoChat@show'));
});
