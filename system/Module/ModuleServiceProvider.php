<?php

namespace Nova\Module;

use Nova\Module\ModuleManager;
use Nova\Support\ServiceProvider;


class ModuleServiceProvider extends ServiceProvider
{
    /**
     * @var bool Indicates if loading of the Provider is deferred.
     */
    protected $defer = false;

    /**
     * Boot the Service Provider.
     */
    public function boot()
    {
        $modules = $this->app['modules'];

        $modules->register();
    }

    /**
     * Register the Service Provider.
     */
    public function register()
    {
        $this->app->bindShared('modules', function ($app)
        {
            return new ModuleManager($app, $app['config']);
        });
    }

    /**
     * Get the Services provided by the Provider.
     *
     * @return string
     */
    public function provides()
    {
        return array('modules');
    }

}
