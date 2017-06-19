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
 * Listener Closure to the Event 'backend.menu.sidebar'.
 */
Event::listen('backend.menu.sidebar', function($menu, $user)
{
	$menu->addItem('dashboard', __d('backend', 'Dashboard'), site_url('admin/dashboard'), 0, 'dashboard');

	if (! $user->hasRole('administrator')) {
		return;
	}

	//
	$menu->addItem('platform', __d('backend', 'Platform'), '', 0, 'cube');

	$menu->addItem('platform.settings', __d('backend', 'Settings'), site_url('admin/settings'));

	$menu->addItem('platform.roles', __d('backend', 'User Roles'), site_url('admin/roles'));

	//
	$menu->addItem('users', __d('backend', 'Users'), '', 1, 'users');

	$menu->addItem('users.list',  __d('backend', 'Users List'), site_url('admin/users'));

	$menu->addItem('users.create',  __d('backend', 'Create a new User'), site_url('admin/users/create'));

});

/**
 * Register the Plugin's Widgets.
 */
Widget::register('Backend\Widgets\DashboardUsersPanel', 'dashboardUsersPanel');
Widget::register('Backend\Widgets\DashboardDummyPanel', 'dashboardDummyPanel');

