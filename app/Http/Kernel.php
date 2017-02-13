<?php

namespace App\Http;

use Nova\Foundation\Http\Kernel as HttpKernel;


class Kernel extends HttpKernel
{
    /**
     * The Application's global HTTP Middleware stack.
     *
     * @var array
     */
    protected $middleware = array(
        'Nova\Foundation\Http\Middleware\CheckForMaintenanceMode',
        'App\Http\Middleware\EncryptCookies',
        'Nova\Cookie\Middleware\AddQueuedCookiesToResponse',
        'Nova\Session\Middleware\StartSession',
        'Nova\Foundation\Http\Middleware\ServeAssetFile',
        'Nova\Foundation\Http\Middleware\SetupLanguage',
        'Nova\View\Middleware\ShareErrorsFromSession',
        'App\Http\Middleware\VerifyCsrfToken',
        'App\Http\Middleware\HandleProfilers',
    );

    /**
     * The Application's route Middleware.
     *
     * @var array
     */
    protected $routeMiddleware = array(
        'auth'       => 'App\Http\Middleware\Authenticate',
        'auth.basic' => 'Nova\Auth\Middleware\AuthenticateWithBasicAuth',
        'guest'      => 'App\Http\Middleware\RedirectIfAuthenticated',
        'referer'    => 'App\Http\Middleware\CheckForReferer',
    );
}
