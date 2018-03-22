<?php

namespace Modules\Roles\Providers;

use Nova\Routing\Router;
use Nova\Package\Support\Providers\RouteServiceProvider as ServiceProvider;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * The controller namespace for the module.
     *
     * @var string|null
     */
    protected $namespace = 'Modules\Roles\Controllers';


    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Nova\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        parent::boot($router);

        //
    }
}
