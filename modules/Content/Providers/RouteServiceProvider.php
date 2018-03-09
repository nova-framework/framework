<?php

namespace Modules\Content\Providers;

use Nova\Routing\Router;
use Nova\Package\Support\Providers\RouteServiceProvider as ServiceProvider;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * The controller namespace for the module.
     *
     * @var string|null
     */
    protected $namespace = 'Modules\Content\Controllers';


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
     * @param  \Nova\Routing\Router $router
     * @return void
     */
    public function map(Router $router)
    {
        $router->group(array('prefix' => 'api', 'middleware' => 'api', 'namespace' => $this->namespace), function ($router)
        {
            require realpath(__DIR__ .'/../Routes/Api.php');
        });

        $router->group(array('middleware' => 'web', 'namespace' => $this->namespace), function ($router)
        {
            require realpath(__DIR__ .'/../Routes/Web.php');
        });
    }
}
