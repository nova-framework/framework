<?php

namespace Demos\Providers;

use Nova\Support\ServiceProvider;


class DemosServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        $basePath = realpath(__DIR__ .'/../');

        // Configure the Package.
        $this->package('Demos', 'demos', $basePath);

        //
        require $basePath .DS .'Bootstrap.php';
    }

    /**
     * Register the Demos module Service Provider.
     *
     * This service provider is a convenient place to register your modules
     * services in the IoC container. If you wish, you may make additional
     * methods or service providers to keep the code more focused and granular.
     *
     * @return void
     */
    public function register()
    {
        // Register additional Service Providers.
        $this->app->register('Demos\Providers\AuthServiceProvider');
        $this->app->register('Demos\Providers\EventServiceProvider');
        $this->app->register('Demos\Providers\RouteServiceProvider');

        /*
        $className = get_class($this);

        //$path = $this->guessPackagePath() .DS .'Demos' .DS;

        $reflection = new \ReflectionClass($this);

        $classPath = $reflection->getFileName();

        $path = realpath(dirname($classPath) .'/../') .DS;

        echo '<pre>' .var_export($path, true) .'</pre>';

        // Retrieve the Composer's Module information.
        $filePath = base_path('vendor/nova-modules.php');

        //
        $modules = array();

        try {
            $data = $this->app['files']->getRequire($filePath);

            if (isset($data['modules']) && is_array($data['modules'])) {
                $modules = array_flip($data['modules']);
            }
        }
        catch (FileNotFoundException $e) {
            // Do nothing.
        }

        $namespace = isset($modules[$path]) ? $modules[$path] : null;

        echo '<pre>' .var_export($namespace, true) .'</pre>';
        */
    }

}
