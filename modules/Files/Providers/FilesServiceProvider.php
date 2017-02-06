<?php

namespace Files\Providers;

use Nova\Support\ServiceProvider;


class FilesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the Files module Service Provider.
     *
     * This service provider is a convenient place to register your modules
     * services in the IoC container. If you wish, you may make additional
     * methods or service providers to keep the code more focused and granular.
     *
     * @return void
     */
    public function register()
    {
        // Configure the Package.
        $this->package('Local/Files', 'files');

        // Register additional Service Providers.
        //$this->app->register('Files\Providers\AuthServiceProvider');
        //$this->app->register('Files\Providers\EventServiceProvider');
    }

}
