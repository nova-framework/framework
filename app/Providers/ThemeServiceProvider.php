<?php

namespace App\Providers;

use Nova\Support\Facades\Config;
use Nova\Support\ServiceProvider;

use InvalidArgumentException;


class ThemeServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to all Theme Service Providers.
     *
     * @var string
     */
    protected $namespace = 'Themes';


    /**
     * Register the Application's Themes.
     *
     * @return void
     */
    public function register()
    {
        $namespace = trim(
            Config::get('view.themes.namespace'), '\\'
        );

        $themes = $this->getInstalledThemes();

        $themes->each(function ($theme) use ($namespace)
        {
            // The main Service Provider from a theme should be named like:
            // Themes\AdminLite\Providers\ThemeServiceProvider

            $provider = sprintf('%s\\%s\\Providers\\ThemeServiceProvider', $namespace, $theme);

            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        });
    }

    protected function getInstalledThemes()
    {
        $themesPath = Config::get('view.themes.path');

        try {
            $paths = $this->app['files']->directories($themesPath);
        }
        catch (InvalidArgumentException $e) {
            $paths = array();
        }

        $themes = array_map(function ($path)
        {
            return basename($path);

        }, $paths);

        return collect($themes);
    }
}
