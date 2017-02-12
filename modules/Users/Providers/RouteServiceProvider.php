<?php

namespace Modules\Users\Providers;

use Nova\Routing\Router;
use Nova\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * @var string
     */
    protected $namespace = '';


    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Nova\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        $basePath = realpath(__DIR__ .'/../Http/') .DS;

        //
        $path = $basePath .'Filters.php';

        $this->loadFiltersFrom($path);

        //
        $path = $basePath .'Routes.php';

        $this->loadRoutesFrom($path);

        //
        parent::boot($router);
    }

}
