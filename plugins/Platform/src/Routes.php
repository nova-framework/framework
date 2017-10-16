<?php

/*
|--------------------------------------------------------------------------
| Plugin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Routes for the Plugin.
|
*/

$router->group(array('prefix' => 'platform'), function($router)
{
    $router->get('/', function()
    {
        dd('This is the Platform plugin index page.');
    });
});
