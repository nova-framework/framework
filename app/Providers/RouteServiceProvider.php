<?php

namespace App\Providers;

use Nova\Routing\Router;
use Nova\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * @var string
     */
    protected $namespace = 'App\Controllers';


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
     * Define the routes for the application.
     *
     * @param  \Nova\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $router->group(array('prefix' => 'api', 'middleware' => 'api', 'namespace' => $this->namespace), function ($router)
        {
            require app_path('Routes/Api.php');
        });

        $router->group(array('middleware' => 'web', 'namespace' => $this->namespace), function ($router)
        {
            require app_path('Routes/Web.php');
        });
    }

}
