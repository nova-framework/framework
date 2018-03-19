<?php

namespace Shared\Routing;

use Shared\Routing\ControllerInspector;

use Nova\Container\Container;
use Nova\Support\ServiceProvider;


class RoutingServiceProvider extends ServiceProvider
{

    /**
     * Boot the Service Provider.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('routing.controller.inspector', function ($app)
        {
            return new ControllerInspector($app['router']);
        });

        $this->registerRouterMacros();
    }

    /**
     * Register the Router extensions.
     *
     * @return void
     */
    protected function registerRouterMacros()
    {
        $app = $this->app;

        $app['router']->macro('controller', function ($uri, $controller, $names = array()) use ($app)
        {
            $inspector = $app->make('routing.controller.inspector');

            $inspector->register($uri, $controller, $names);
        });
    }
}
