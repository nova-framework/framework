<?php

namespace Modules\Platform\Providers;

use Nova\Foundation\AliasLoader;
use Nova\Package\Support\Providers\ModuleServiceProvider as ServiceProvider;


class ModuleServiceProvider extends ServiceProvider
{
    /**
     * The additional provider class names.
     *
     * @var array
     */
    protected $providers = array(
        'Modules\Platform\Providers\AuthServiceProvider',
        'Modules\Platform\Providers\EventServiceProvider',
        'Modules\Platform\Providers\RouteServiceProvider',
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
        $this->package('Modules/Platform', 'platform', $path);

        // Bootstrap the Module.
        $path = $path .DS .'Bootstrap.php';

        $this->bootstrapFrom($path);
    }

    /**
     * Register the System module Service Provider.
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

        // Register the aliases for Facades.
        $loader = AliasLoader::getInstance();

        $loader->alias('Action', 'Modules\Platform\Support\Facades\Action');
        $loader->alias('Filter', 'Modules\Platform\Support\Facades\Filter');
    }
}
