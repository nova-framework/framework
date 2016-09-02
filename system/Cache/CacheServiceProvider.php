<?php

namespace Cache;

use Support\ServiceProvider;


class CacheServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('cache', function($app)
        {
            return new CacheManager($app);
        });

        $this->app->bindShared('cache.store', function($app)
        {
            return $app['cache']->driver();
        });

        $this->app->bindShared('memcached.connector', function()
        {
            return new MemcachedConnector;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'cache', 'cache.store', 'memcached.connector'
        );
    }

}
