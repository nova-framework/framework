<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define static routes. */

// The Framework's Language Changer.
$router->get('language/{code}', array('middleware' => 'referer', 'uses' => 'Language@update'))
    ->where('code', '([a-z]{2})');

// The CRON runner.
$router->get('cron/{token}', array('uses' => 'CronRunner@index'));


// The Adminstration Routes.
$router->group(array('prefix' => 'admin', 'namespace' => 'Admin'), function($router)
{
    // Notifications
    $router->get('notifications', array('middleware' => 'auth', 'uses' => 'Notifications@index'));

    // The Site Settings.
    $router->get( 'settings', array('middleware' => 'auth', 'uses' => 'Settings@index'));
    $router->post('settings', array('middleware' => 'auth', 'uses' => 'Settings@store'));
});
