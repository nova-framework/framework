<?php

namespace AcmeCorp\Widgets\Providers;

use Nova\Foundation\AliasLoader;
use Nova\Support\ServiceProvider;

use AcmeCorp\Widgets\Support\WidgetManager;


class PluginServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__ .'/../');

        // Configure the Package.
        $this->package('AcmeCorp/Widgets', 'widgets', $path);
    }

    /**
     * Register the Widgets plugin Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('widgets', function($app)
        {
            return new WidgetManager($app);
        });

        // Register the Facades.
        $loader = AliasLoader::getInstance();

        $loader->alias('Widget', 'AcmeCorp\Widgets\Support\Facades\Widget');
    }
}
