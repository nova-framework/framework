<?php

namespace Modules\WebChat\Providers;

use Nova\Routing\Router;
use Nova\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * The controller namespace for the module.
     *
     * @var string|null
     */
    protected $namespace = 'Modules\WebChat\Http\Controllers';


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

    /**
     * Define the routes for the module.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    public function map(Router $router)
    {
        $router->group(array('namespace' => $this->namespace), function($router)
        {
            require module_path('WebChat', 'Http/Routes.php');
        });
    }
}
