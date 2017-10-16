<?php

namespace AcmeCorp\Backend\Providers;

use Nova\Plugins\Support\Providers\PluginServiceProvider as ServiceProvider;


class PluginServiceProvider extends ServiceProvider
{
    /**
     * The additional provider class names.
     *
     * @var array
     */
    protected $providers = array(
        //'AcmeCorp\Backend\Providers\AuthServiceProvider',
        //'AcmeCorp\Backend\Providers\EventServiceProvider',
        'AcmeCorp\Backend\Providers\RouteServiceProvider',
    );


    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__ .'/../');

        // Configure the Package.
        $this->package('AcmeCorp/Backend', 'backend', $path);

        // Bootstrap the Plugin.
        require $path .DS .'Bootstrap.php';
    }

    /**
     * Register the Backend plugin Service Provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        //
    }

}
