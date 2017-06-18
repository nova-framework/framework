<?php

// The default Auth Routes.
$router->get( 'auth/login',  array('middleware' => 'guest', 'uses' => 'Authorize@login'));
$router->post('auth/login',  array('middleware' => 'guest', 'uses' => 'Authorize@postLogin'));
$router->post('auth/logout', array('middleware' => 'auth',  'uses' => 'Authorize@logout'));

// The Adminstration Routes.
$router->group(array('prefix' => 'admin', 'middleware' => 'auth', 'namespace' => 'Admin'), function($router)
{
	// The User's Dashboard
	$router->get('/',			'Dashboard@index');
	$router->get('dashboard',	'Dashboard@index');

	$router->get('dashboard/notify', 'Dashboard@notify');

	// Server Side Processor for Dashboard's Online Users DataTable.
	$router->post('dashboard/data', 'Dashboard@data');

	// The Platform Settings.
	$router->get( 'settings',	'Settings@index');
	$router->post('settings',	'Settings@store');

	// The User's Profile.
	$router->get( 'profile',	'Profile@index');
	$router->post('profile',	'Profile@update');

	// Messages.
	$router->get( 'messages',					'Messages@index');
	$router->get( 'messages/create', 			'Messages@create');
	$router->post('messages',					'Messages@store');
	$router->get( 'messages/{threadId}',		'Messages@show');
	//$router->post('messages/{postId}/destroy',	'Messages@destroy');

	$router->post('messages/{threadId}',		'Messages@reply');

	// Notifications
	$router->get('notifications',		'Notifications@index');

	// Server Side Processor for Users DataTable.
	$router->post('users/data',			'Users@data');

	// The Users CRUD.
	$router->get( 'users',				'Users@index');
	$router->get( 'users/create',		'Users@create');
	$router->post('users',				'Users@store');
	$router->get( 'users/{id}',			'Users@show');
	$router->get( 'users/{id}/edit',	'Users@edit');
	$router->post('users/{id}',			'Users@update');
	$router->post('users/{id}/destroy',	'Users@destroy');


	// Server Side Processor for Roles DataTable.
	$router->post('roles/data', 		'Roles@data');

	// The Roles CRUD.
	$router->get( 'roles',				'Roles@index');
	$router->get( 'roles/create',		'Roles@create');
	$router->post('roles',				'Roles@store');
	$router->get( 'roles/{id}',			'Roles@show');
	$router->get( 'roles/{id}/edit',	'Roles@edit');
	$router->post('roles/{id}',			'Roles@update');
	$router->post('roles/{id}/destroy',	'Roles@destroy');
});
