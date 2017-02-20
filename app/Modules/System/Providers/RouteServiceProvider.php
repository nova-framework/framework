<?php

namespace App\Modules\System\Providers;

use Nova\Routing\Router;
use Nova\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * The controller namespace for the module.
     *
     * @var string|null
     */
    protected $namespace = 'App\Modules\System\Controllers';


    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Nova\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        $basePath = realpath(__DIR__ .'/../');

        // Load the Route Filters.
        $path = $basePath .DS .'Filters.php';

        $this->loadFiltersFrom($path);

        //
        parent::boot($router);
    }

    /**
     * Define the routes for the module.
     *
     * @param  \Nova\Routing\Router $router
     * @return void
     */
    public function map(Router $router)
    {
        $path = realpath(__DIR__ .'/../');

        $router->group(array('namespace' => $this->namespace), function($router) use ($path)
        {
            require $path .DS .'Routes.php';
        });
    }
}
