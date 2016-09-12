<?php

namespace Module;

use Config\Repository as Config;
use Foundation\Application;
use Support\Collection;
use Support\Str;


class ModuleManager
{
    /**
     * @var \Foundation\Application
     */
    protected $app;

    /**
     * @var \Config\Repository
     */
    protected $config;


    /**
     * Create a new ModuleRepository instance.
     *
     * @param Application         $app
     * @param RepositoryInterface $repository
     */
    public function __construct(Application $app, Config $config)
    {
        $this->app = $app;

        $this->config = $config;
    }

    /**
     * Register the module service provider file from all modules.
     *
     * @return mixed
     */
    public function register()
    {
        $modules = $this->getModules();

        $modules->each(function($properties)
        {
            $enabled = array_get($properties,'enabled', true);

            if ($enabled) {
                $this->registerServiceProvider($properties);

                $this->autoloadFiles($properties);
            }
        });
    }

    /**
     * Register the Module Service Provider.
     *
     * @param string $config
     *
     * @return string
     */
    protected function registerServiceProvider($properties)
    {
        $name = array_get($properties, 'name');

        $file = $this->getModulesPath() .DS .$name .DS .'Providers' .DS .$name .'ServiceProvider.php';

        // Calculate the name of Service Provider, including the namespace.
        $serviceProvider = $this->getNamespace() ."\\{$name}\\Providers\\{$name}ServiceProvider";

        if (class_exists($serviceProvider)) {
            $this->app->register($serviceProvider);
        }
    }

    /**
     * Autoload custom module files.
     *
     * @param array $config
     */
    protected function autoloadFiles($properties)
    {
        $names = array('config', 'events', 'filters', 'routes');

        // Calculate the names of the files to be autoloaded.
        $autoload = array_get($properties, 'autoload');

        if (is_array($autoload) && ! empty($autoload)) {
            $names = array_values(array_intersect($names, $autoload));
        }

        array_push($names, 'bootstrap');

        // Calculate the Modules path.
        $module = array_get($properties, 'name');

        $basePath = $this->getModulesPath() .DS .$module .DS;

        foreach ($names as $name) {
            $path = $basePath .ucfirst($name) .'.php';

            if (is_readable($path)) require $path;
        }
    }

    public function getModulesPath()
    {
        $path = $this->config->get('modules.path', APPDIR .'Modules');

        return str_replace('/', DS, realpath($path));
    }

    public function getNamespace()
    {
        return $this->config->get('modules.namespace', 'App\Modules\\');
    }

    public function getModules()
    {
        $modules = $this->config->get('modules.repository');

        $modules = array_map(function($name, $config)
        {
            return array_merge(array(
                'name'      => $name,
                'slug'      => isset($config['slug'])    ? $config['slug']    : Str::slug($name),
                'enabled'   => isset($config['enabled']) ? $config['enabled'] : true,
                'order'     => isset($config['order'])   ? $config['order']   : 9001,
            ), $config);
        }, array_keys($modules), $modules);

        return Collection::make($modules)->sortBy('order');
    }

}
