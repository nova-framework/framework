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
    // The Site Logs.
    $router->get( 'logs/{group?}', array('middleware' => 'auth', 'uses' => 'Logs@index'));
    $router->post('logs/clear',    array('middleware' => 'auth', 'uses' => 'Logs@clear'));
});
