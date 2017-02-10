<?php

namespace Plugins\Cron;

use Nova\Foundation\AliasLoader;
use Nova\Support\ServiceProvider;

use Plugins\Cron\CronManager;


class CronServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the Provider is deferred.
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
        // Register the Plugin's Facades.
        $loader = AliasLoader::getInstance();

        $loader->alias('Cron', 'Plugins\Cron\Facades\Cron');
    }

    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('cron', function($app)
        {
            return new CronManager($app['events']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('cron');
    }
}
