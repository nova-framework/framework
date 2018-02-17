<?php

namespace Modules\Fields\Providers;

use Nova\Foundation\AliasLoader;
use Nova\Modules\Support\Providers\ModuleServiceProvider as ServiceProvider;

use Modules\Fields\Types\Registry as TypeRegistry;


class ModuleServiceProvider extends ServiceProvider
{
    /**
     * The additional provider class names.
     *
     * @var array
     */
    protected $providers = array(
        'Modules\Fields\Providers\AuthServiceProvider',
        'Modules\Fields\Providers\EventServiceProvider',
        'Modules\Fields\Providers\RouteServiceProvider',
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
        $this->package('Modules/Fields', 'fields', $path);

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
     * @param  \Modules\Fields\Meta\Registry $registry
     * @return void
     */
    public function registerDefaultTypes(TypeRegistry $registry)
    {
        $types = array(
            new \Modules\Fields\Types\StringType,
            new \Modules\Fields\Types\IntegerType,
            new \Modules\Fields\Types\BooleanType,
            new \Modules\Fields\Types\ArrayType,
            new \Modules\Fields\Types\DoubleType,
            new \Modules\Fields\Types\FileType,
            new \Modules\Fields\Types\ImageType,
        );

        $registry->register($types);
    }

    protected function registerFacades()
    {
        $loader = AliasLoader::getInstance();

        $loader->alias('FieldTypeRegistry', 'Modules\Fields\Support\Facades\FieldTypeRegistry');
    }
}
