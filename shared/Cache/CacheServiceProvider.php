<?php

namespace Shared\Cache;

use Nova\Support\ServiceProvider;

use Shared\Cache\TaggableFileStore;


class CacheServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $config = $this->app['config'];

        $this->app['cache']->extend('tagged_file', function ($app) use ($config)
        {
            $path = $config->get('cache.path');

            $options = $config->get('cache.taggedFile', array());

            return $app['cache']->repository(
                new TaggableFileStore($app['files'], $path, $options)
            );
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
