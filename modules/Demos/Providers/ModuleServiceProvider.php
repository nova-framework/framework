<?php

namespace Modules\Demos\Providers;

use Nova\Module\Support\Providers\ModuleServiceProvider as ServiceProvider;


class ModuleServiceProvider extends ServiceProvider
{
    /**
     * The additional provider class names.
     *
     * @var array
     */
    protected $providers = array(
        'Modules\Demos\Providers\AuthServiceProvider',
        'Modules\Demos\Providers\EventServiceProvider',
        'Modules\Demos\Providers\RouteServiceProvider',
    );


    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
        $path = realpath(__DIR__ .'/../');

        // Configure the Package.
        $this->package('Modules/Demos', 'demos', $path);

        // Bootstrap the Package.
        $path = $path .DS .'Bootstrap.php';

        $this->bootstrapFrom($path);

        // Register the Widgets namespace.
        $this->app['widgets']->register('Modules\Demos\Widgets');
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
        parent::register();

        //

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
