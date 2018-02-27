<?php

namespace App\Providers;

use Nova\Filesystem\FileNotFoundException;
use Nova\Support\Arr;
use Nova\Support\Str;
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
     * Register the Application's Packages.
     *
     * @return void
     */
    public function register()
    {
        $packages = $this->getInstalledPackages();

        foreach ($packages as $package) {
            $namespace = str_replace('/', '\\', $package);

            // The main Service Provider from a package should be named like:
            // AcmeCorp\Pages\Providers\PackageServiceProvider
            //
            // If it does not exists, we will look for a class with name like:
            // AcmeCorp\Pages\PageServiceProvider

            $provider = $namespace .'\\Providers\\PackageServiceProvider';

            if (! class_exists($provider)) {
                list (, $name) = explode('/', $package);

                $provider = sprintf('%s\\%sServiceProvider', $namespace, Str::singular($name));

                if (! class_exists($provider)) {
                    continue;
                }
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
