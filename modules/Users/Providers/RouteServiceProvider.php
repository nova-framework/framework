<?php

namespace Modules\Users\Providers;

use Nova\Package\Support\Providers\RouteServiceProvider as ServiceProvider;
use Nova\Routing\Router;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * The controller namespace for the module.
     *
     * @var string|null
     */
    protected $namespace = 'Modules\Users\Controllers';


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
