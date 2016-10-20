<?php

namespace Nova\Cron;

use Nova\Support\ServiceProvider;

use Nova\Cron\Manager;


class CronServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the Provider is deferred.
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
        $this->app->bindShared('cron', function($app)
        {
            return new Manager($app['events']);
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
