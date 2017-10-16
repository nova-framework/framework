<?php

namespace AcmeCorp\FileManager\Providers;

use Nova\Plugins\Support\Providers\PluginServiceProvider as ServiceProvider;


class PluginServiceProvider extends ServiceProvider
{
    /**
     * The additional provider class names.
     *
     * @var array
     */
    protected $providers = array(
        //'AcmeCorp\FileManager\Providers\AuthServiceProvider',
        //'AcmeCorp\FileManager\Providers\EventServiceProvider',
        'AcmeCorp\FileManager\Providers\RouteServiceProvider'
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
        $this->package('AcmeCorp/FileManager', 'file_manager', $path);

        // Bootstrap the Plugin.
        require $path .DS .'Bootstrap.php';
    }

    /**
     * Register the FileManager plugin Service Provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        //
    }

}
