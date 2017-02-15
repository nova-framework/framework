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
    // The User's Dashboard.
    $router->get('/',         array('middleware' => 'auth', 'uses' => 'Dashboard@index'));
    $router->get('dashboard', array('middleware' => 'auth', 'uses' => 'Dashboard@index'));
});
