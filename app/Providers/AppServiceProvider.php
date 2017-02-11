<?php

namespace App\Providers;

use Nova\Foundation\Support\Providers\AppServiceProvider as ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__ .'/../') .DS .'Bootstrap.php';

        $this->bootstrapFrom($path);

        //
    }

    /**
     * Register the Application's Service Provider.
     *
     * This service provider is a convenient place to register your modules
     * services in the IoC container. If you wish, you may make additional
     * methods or service providers to keep the code more focused and granular.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
