<?php

namespace App\Providers;

use Nova\Support\ServiceProvider;


class ThemeServiceProvider extends ServiceProvider
{

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
        $themesPath = APPDIR .'Themes';

        try {
            $paths = $this->app['files']->directories($themesPath);
        }
        catch (InvalidArgumentException $e) {
            // Do nothing.
            $paths = array();
        }

        foreach ($paths as $path) {
            $theme = basename($path);

            $this->registerServiceProvider($theme);
        }
    }

    /**
     * Register the Theme Service Provider.
     *
     * @param string $theme
     *
     * @return void
     *
     * @throws \Nova\Module\FileMissingException
     */
    protected function registerServiceProvider($theme)
    {
        // Calculate the name of Service Provider, including the namespace.
        $serviceProvider = "App\\Themes\\{$theme}\\Providers\\ThemeServiceProvider";

        if (class_exists($serviceProvider)) {
            $this->app->register($serviceProvider);
        }
    }
}
