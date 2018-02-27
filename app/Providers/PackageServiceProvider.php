<?php

namespace App\Providers;

use Nova\Filesystem\FileNotFoundException;
use Nova\Support\Arr;
use Nova\Support\ServiceProvider;


class PackageServiceProvider extends ServiceProvider
{
    /**
     * The path to vendor file of the known Nova Packages.
     *
     * @var string
     */
    protected $configPath = BASEPATH .'vendor' .DS .'nova-packages.php';


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
        $packages = $this->getInstalledPackages();

        foreach ($packages as $package) {
            $namespace = str_replace('/', '\\', $package);

            // The main Service Provider from a package usually have a name like:
            // AcmeCorp\Pages\Providers\PackageServiceProvider

            $provider = $namespace .'\\Providers\\PackageServiceProvider';

            if (class_exists($provider)) {
                //
            }

            // If it does not exists, we will look for an altenative class like:
            // AcmeCorp\PagesServiceProvider

            else if (! class_exists($provider = $namespace .'ServiceProvider')) {
                continue;
            }

            $this->app->register($provider);
        }
    }

    protected function getInstalledPackages()
    {
        try {
            $config = $this->app['files']->getRequire($this->configPath);
        }
        catch (FileNotFoundException $e) {
            $config = array();
        }

        if (is_array($config) && ! empty($config)) {
            $packages = Arr::get($config, 'packages', array());

            if (is_array($packages) && ! empty($packages)) {
                return array_keys($packages);
            }
        }

        return array();
    }
}
