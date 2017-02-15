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
    $router->get('files',           array('middleware' => 'auth', 'uses' => 'Files@index'));
    $router->any('files/connector', array('middleware' => 'auth', 'uses' => 'Files@connector'));

    // Thumbnails Files serving.
    $router->get('files/thumbnails/{file}', array('middleware' => 'auth', 'uses' => 'Files@thumbnails'));

    // Preview Files serving.
    $router->get('files/preview/{path}', array('middleware' => 'auth', 'uses' => 'Files@preview'))->where('path', '(.*)');
});
