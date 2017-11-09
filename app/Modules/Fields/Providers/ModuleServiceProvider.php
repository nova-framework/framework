<?php

namespace App\Modules\Fields\Providers;

use Nova\Foundation\AliasLoader;
use Nova\Modules\Support\Providers\ModuleServiceProvider as ServiceProvider;

use App\Modules\Fields\Types\Registry as TypeRegistry;


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

        // Register the Types Registry.
        $this->registerTypeRegistry();

        //$this->registerFacades();
    }

    /**
     * Register the meta item value type registry.
     *
     * @return void
     */
    public function registerTypeRegistry()
    {
        $this->app->singleton(TypeRegistry::class, function ($app)
        {
            $registry = new TypeRegistry();

            $this->registerDefaultTypes($registry);

            return $registry;
        });
    }

    /**
     * Register the default item value types with the registry.
     *
     * @param  \App\Modules\Fields\Meta\Registry $registry
     * @return void
     */
    public function registerDefaultTypes(TypeRegistry $registry)
    {
        $types = array(
            new \App\Modules\Fields\Types\StringType,
            new \App\Modules\Fields\Types\IntegerType,
            new \App\Modules\Fields\Types\BooleanType,
            new \App\Modules\Fields\Types\ArrayType,
            new \App\Modules\Fields\Types\DoubleType,
            new \App\Modules\Fields\Types\FileType,
            new \App\Modules\Fields\Types\ImageType,
        );

        $registry->register($types);
    }

    protected function registerFacades()
    {
        $loader = AliasLoader::getInstance();

        $loader->alias('FieldTypeRegistry', 'App\Modules\Fields\Support\Facades\FieldTypeRegistry');
    }
}
