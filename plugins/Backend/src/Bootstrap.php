<?php

use Nova\Http\Request;

use Backend\Controllers\BaseController as BackendController;


/**
 * Role-based Authorization Middleware.
 */
Route::middleware('role', function(Request $request, Closure $next, $role)
{
	$roles = array_slice(func_get_args(), 2);

	//
	$guard = Config::get('auth.default', 'web');

	$user = Auth::guard($guard)->user();

	if (! is_null($user) && ! $user->hasRole($roles)) {
		$uri = Config::get("auth.guards.{$guard}.paths.dashboard", 'admin/dashboard');

		$status = __('You are not authorized to access this resource.');

		return Redirect::to($uri)->with('warning', $status);
	}

	return $next($request);
});


/**
 * Listener Closure to the Event 'backend.menu'.
 */
Event::listen('backend.menu', function($user)
{
	$items = array(
		array(
			'uri'		=> 'admin/dashboard',
			'title'		=> __d('backend', 'Dashboard'),
			'label'		=> '',
			'icon'		=> 'dashboard',
			'weight'	=> 0,
		),
	);

	if (! $user->hasRole('administrator')) {
		return $items;
	}

	$items = array_merge($items, array(
		array(
			'title'  => __d('backend', 'Platform'),
			'icon'   => 'cube',
			'weight' => 0,
			'children' => array(
				array(
					'uri'		=> 'admin/settings',
					'title'		=> __d('backend', 'Site Configuration'),
					'label'		=> '',
					'weight'	=> 0,
				),
			),
		),
		array(
			'title'  => __d('backend', 'Users'),
			'icon'   => 'users',
			'weight' => 1,
			'children' => array(
				array(
					'uri'		=> 'admin/users',
					'title'		=> __d('backend', 'Users List'),
					'label'		=> '',
					'weight'	=> 0,
				),
				array(
					'uri'		=> 'admin/roles',
					'title'		=> __d('backend', 'User Roles'),
					'label'		=> '',
					'weight'	=> 2,
				),
			),
		),
	));

	return $items;
});


/**
 * Register the Plugin's Widgets.
 */
Widget::register('Backend\Widgets\DashboardUsersPanel', 'dashboardUsersPanel');
Widget::register('Backend\Widgets\DashboardDummyPanel', 'dashboardDummyPanel');

