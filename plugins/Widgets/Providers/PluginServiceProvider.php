<?php

namespace Plugins\Widgets\Providers;

use Nova\Foundation\AliasLoader;
use Nova\Support\ServiceProvider;

use Plugins\Widgets\Factory as WidgetFactory;


class PluginServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__);

        // Configure the Package.
        $this->package('Plugins/Widgets', 'widgets', $path);

        // Register the Plugin's Facades.
        $loader = AliasLoader::getInstance();

        $loader->alias('Widget', 'Plugins\Widgets\Facades\Widget');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('widgets', function($app)
        {
            return new WidgetFactory($app['app']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('widgets');
    }
}
