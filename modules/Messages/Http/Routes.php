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
    // Messages
    $router->get( 'messages',                  array('middleware' => 'auth', 'uses' => 'Messages@index'));
    $router->get( 'messages/create',           array('middleware' => 'auth', 'uses' => 'Messages@create'));
    $router->post('messages',                  array('middleware' => 'auth', 'uses' => 'Messages@store'));
    $router->get( 'messages/{threadId}',       array('middleware' => 'auth', 'uses' => 'Messages@show'));
    //$router->post('messages/{postId}/destroy', array('middleware' => 'auth', 'uses' => 'Messages@destroy'));

    $router->post('messages/{threadId}', array('middleware' => 'auth', 'uses' => 'Messages@reply'));
});
