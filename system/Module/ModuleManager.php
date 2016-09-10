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

        $modules->each(function($config)
        {
            if ($config['enabled'] == false) continue;

            //
            $this->registerServiceProvider($config);

            $this->autoloadFiles($config);
        });
    }

    /**
     * Register the Module Service Provider.
     *
     * @param string $config
     *
     * @return string
     */
    protected function registerServiceProvider($config)
    {
        $name = $config['name'];

        // Calculate the name of Service Provider, including the namespace.
        $serviceProvider = $this->getModulesNamespace() ."\\{$name}\\Providers\\{$name}ServiceProvider";

        if (class_exists($serviceProvider)) {
            $this->app->register($serviceProvider);
        }
    }

    /**
     * Autoload custom module files.
     *
     * @param array $config
     */
    protected function autoloadFiles($config)
    {
        $autoload = array('config', 'events', 'filters', 'routes', 'bootstrap');

        // Calculate the names of the files to be autoloaded.
        if (isset($config['autoload']) && is_array($config['autoload'])) {
            $autoload = array_values(array_intersect($config['autoload'], $autoload));
        }

        // Calculate the Modules path.
        $module = $config['name'];

        $basePath = $this->getModulesPath() .DS .$module .DS;

        foreach ($autoload as $name) {
            $path = $basePath .ucfirst($name) .'.php';

            if (is_readable($path)) require $path;
        }
    }

    public function getModulesPath()
    {
        return $this->config->get('modules.path');
    }

    public function getModulesNamespace()
    {
        return $this->config->get('modules.namespace');
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
