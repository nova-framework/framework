<?php

namespace Users\Providers;

use Nova\Support\ServiceProvider;


class UsersServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        $basePath = realpath(__DIR__ .'/../');

        // Configure the Package.
        $this->package('Users', 'users', $basePath);

        //
        require $basePath .DS .'Bootstrap.php';
    }

    /**
     * Register the Users module Service Provider.
     *
     * This service provider is a convenient place to register your modules
     * services in the IoC container. If you wish, you may make additional
     * methods or service providers to keep the code more focused and granular.
     *
     * @return void
     */
    public function register()
    {
        // Register additional Service Providers.
        $this->app->register('Users\Providers\AuthServiceProvider');
        $this->app->register('Users\Providers\EventServiceProvider');
        $this->app->register('Users\Providers\RouteServiceProvider');
    }

}
