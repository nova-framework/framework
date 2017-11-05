<?php

namespace App\Modules\Users\Providers;

use Nova\Routing\Router;
use Nova\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * The controller namespace for the module.
     *
     * @var string|null
     */
    protected $namespace = 'App\Modules\Users\Controllers';


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
            $this->loadRoutesFor('api');
        });

        $router->group(array('middleware' => 'web', 'namespace' => $this->namespace), function ($router)
        {
            $this->loadRoutesFor('web');
        });
    }

    protected function loadRoutesFor($type)
    {
        require realpath(__DIR__ .'/../Routes') .DS .ucfirst($type) .'.php';
    }
}
