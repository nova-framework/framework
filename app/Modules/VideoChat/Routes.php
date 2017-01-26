<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define static routes. */

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'App\Modules\VideoChat\Controllers\Admin'), function()
{
    // Chat
    Route::get( 'chat', array('before' => 'auth', 'uses' => 'VideoChat@chat'));

    // Video Chat
    Route::get( 'chat/video',          array('before' => 'auth',      'uses' => 'VideoChat@index'));
    Route::post('chat/video',          array('before' => 'auth|csrf', 'uses' => 'VideoChat@create'));
    Route::get( 'chat/video/{roomId}', array('before' => 'auth',      'uses' => 'VideoChat@show'));
});
