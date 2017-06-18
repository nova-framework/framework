<?php

/*
|--------------------------------------------------------------------------
| Plugin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Routes for the Plugin.
|
*/


// The Adminstration Routes.
$router->group(array('prefix' => 'admin', 'middleware' => 'auth', 'namespace' => 'Admin'), function($router)
{
	$router->get('files',			'Files@index');
	$router->any('files/connector', 'Files@connector');

	// Thumbnails Files serving.
	$router->get('files/thumbnails/{file}', 'Files@thumbnails');

	// Preview Files serving.
	$router->get('files/preview/{path}', 'Files@preview')->where('path', '(.*)');
});

