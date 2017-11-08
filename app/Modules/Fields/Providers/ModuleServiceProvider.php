<?php

namespace App\Modules\Fields\Providers;

use Nova\Modules\Support\Providers\ModuleServiceProvider as ServiceProvider;

use App\Modules\Fields\Support\FieldRegistry;


class ModuleServiceProvider extends ServiceProvider
{
    /**
     * The additional provider class names.
     *
     * @var array
     */
    protected $providers = array(
        'App\Modules\Fields\Providers\AuthServiceProvider',
        'App\Modules\Fields\Providers\EventServiceProvider',
        'App\Modules\Fields\Providers\RouteServiceProvider',
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
        $this->package('Module/Fields', 'fields', $path);

        // Bootstrap the Package.
        $path = $path .DS .'Bootstrap.php';

        $this->bootstrapFrom($path);
    }

    /**
     * Register the Fields module Service Provider.
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

        // Register the Fields Registry.
        $this->registerFieldRegistry();
    }

    /**
     * Register the meta item value type registry.
     *
     * @return void
     */
    public function registerFieldRegistry()
    {
        $this->app->singleton(FieldRegistry::class, function ($app)
        {
            $registry = new FieldRegistry();

            $this->registerDefaultFields($registry);

            return $registry;
        });
    }

    /**
     * Register the default item value types with the registry.
     *
     * @param  \App\Modules\Fields\Meta\Registry $registry
     * @return void
     */
    public function registerDefaultFields(FieldRegistry $registry)
    {
        $types = array(
            new \App\Modules\Fields\Fields\StringField,
            new \App\Modules\Fields\Fields\IntegerField,
            new \App\Modules\Fields\Fields\BooleanField,
            new \App\Modules\Fields\Fields\ArrayField,
            new \App\Modules\Fields\Fields\DoubleField,
        );

        $registry->register($types);
    }
}
