<?php

namespace AcmeCorp\Platform\Providers;

use Nova\Plugins\Support\Providers\PluginServiceProvider as ServiceProvider;


class PluginServiceProvider extends ServiceProvider
{
    /**
     * The additional provider class names.
     *
     * @var array
     */
    protected $providers = array(
        'AcmeCorp\Platform\Providers\AuthServiceProvider',
        'AcmeCorp\Platform\Providers\EventServiceProvider',
        'AcmeCorp\Platform\Providers\RouteServiceProvider'
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
        $this->package('AcmeCorp/Platform', 'platform', $path);

        // Bootstrap the Plugin.
        require $path .DS .'Bootstrap.php';
    }

    /**
     * Register the Platform plugin Service Provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        //
    }

}
