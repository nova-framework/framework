<?php

namespace Shared\DataTable;

use Nova\Support\ServiceProvider;


class DataTableServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Register the AsyncQueue plugin Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('dataTables', function ($app)
        {
            return new Factory($app['request'], $app['response.factory']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('dataTables');
    }
}
