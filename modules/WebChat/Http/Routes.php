<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define static routes. */

// The Adminstration Routes.
$router->group(array('prefix' => 'admin', 'namespace' => 'Admin'), function($router)
{
    // Chat
    $router->get( 'chat', array('middleware' => 'auth', 'uses' => 'Chat@index'));

    // Video Chat
    $router->get( 'chat/video',          array('middleware' => 'auth', 'uses' => 'VideoChat@index'));
    $router->post('chat/video',          array('middleware' => 'auth', 'uses' => 'VideoChat@create'));
    $router->get( 'chat/video/{roomId}', array('middleware' => 'auth', 'uses' => 'VideoChat@show'));
});
