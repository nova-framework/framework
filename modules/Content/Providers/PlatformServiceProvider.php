<?php

namespace Modules\Content\Providers;

use Nova\Support\ServiceProvider;


class PlatformServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__ .'/../');

        require $path .DS .'Platform.php';
    }

    /**
     * Register the Application's Service Provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
