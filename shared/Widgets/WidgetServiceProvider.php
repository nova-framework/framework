<?php

namespace Shared\Widgets;

use Nova\Foundation\AliasLoader;
use Nova\Support\ServiceProvider;

use Shared\Widgets\WidgetManager;


class WidgetServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('widgets', function($app)
        {
            return new WidgetManager($app['request'], $app);
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
