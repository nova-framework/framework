<?php

namespace Plugins\Helpers\Providers;

use Nova\Support\ServiceProvider;


class PluginServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the Application events.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $path = realpath(__DIR__ .'/../');

        // Configure the Package.
        $this->package('Plugins/Helpers', 'helpers', $path);

        //
    }
}
