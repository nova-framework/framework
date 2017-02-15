<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define static routes. */

// The Demo Routes
$router->group(array('prefix' => 'demo'), function($router)
{
    $router->get('database', 'Demos@database');
    $router->get('events',   'Demos@events');
    $router->get('mailer',   'Demos@mailer');
    $router->get('session',  'Demos@session');
    $router->get('validate', 'Demos@validate');
    $router->get('paginate', 'Demos@paginate');
    $router->get('cache',    'Demos@cache');

    $router->get('password/{password}', 'Demos@password');

    //
    $router->get('request/{param1?}/{param2?}/{slug?}', 'Demos@request')
        ->where('slug', '(.*)');

    $router->get('test/{param1?}/{param2?}/{param3?}/{slug?}', array('before' => 'test', 'uses' => 'Demos@test'))
        ->where('slug', '(.*)');
});


// A catch-all Route - will match any URI, while using any HTTP Method.
//$router->any('{slug}', 'App\Controllers\Demo@catchAll')->where('slug', '(.*)');
