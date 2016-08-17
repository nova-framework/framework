<?php

namespace App\Modules\Cron\Providers;

use App\Modules\Cron\Core\Manager;

use Support\ServiceProvider;


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
