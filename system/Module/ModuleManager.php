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
            if (isset($config['enabled']) && is_bool($config['enabled'])) {
                $enabled = $config['enabled'];
            } else {
                $enabled = true;
            }

            if (! $enabled) continue;

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
        $namespace = $config['namespace'];

        // Calculate the name of Service Provider, including the namespace.
        $serviceProvider = $this->getModulesNamespace() ."\\{$namespace}\\Providers\\{$namespace}ServiceProvider";

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
        $autoload = array('config', 'events', 'filters', 'routes');

        // Calculate the names of the files to be autoloaded.
        if (isset($config['autoload']) && is_array($config['autoload'])) {
            $autoload = array_values(array_intersect($config['autoload'], $autoload));
        }

        array_push($autoload, 'bootstrap');

        // Calculate the Modules path.
        $namespace = $config['namespace'];

        $basePath = $this->getModulesPath() .DS .$namespace .DS;

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
            $result = array_merge(array(
                'name'      => $name,
                'namespace' => isset($config['namespace']) ? $config['namespace'] : $name,
                'slug'      => isset($config['slug']) ? $config['slug'] : Str::slug($name),
            ), $config);

            return $result;
        }, array_keys($modules), $modules);

        return Collection::make($modules)->sortBy('order');
    }

}
