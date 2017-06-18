<?php

namespace App\Http;

use Nova\Foundation\Http\Kernel as HttpKernel;


class Kernel extends HttpKernel
{
	/**
	 * The Application's Middleware stack.
	 *
	 * @var array
	 */
	protected $middleware = array(
		'Nova\Foundation\Http\Middleware\CheckForMaintenanceMode',
		'Nova\Routing\Middleware\DispatchAssetFiles',
	);

	/**
	 * The Application's route Middleware Groups.
	 *
	 * @var array
	 */
	protected $middlewareGroups = array(
		'web' => array(
			'App\Http\Middleware\HandleProfilers',
			'App\Http\Middleware\EncryptCookies',
			'Nova\Cookie\Middleware\AddQueuedCookiesToResponse',
			'Nova\Session\Middleware\StartSession',
			'Nova\Foundation\Http\Middleware\SetupLanguage',
			'Nova\View\Middleware\ShareErrorsFromSession',
			'App\Http\Middleware\VerifyCsrfToken',
		),
		'api' => array(
			'throttle:60,1',
		)
	);

	/**
	 * The Application's route Middleware.
	 *
	 * @var array
	 */
	protected $routeMiddleware = array(
		'auth'		=> 'Nova\Auth\Middleware\Authenticate',
		'guest'		=> 'App\Http\Middleware\RedirectIfAuthenticated',
		'throttle'	=> 'Nova\Routing\Middleware\ThrottleRequests',
	);
}
