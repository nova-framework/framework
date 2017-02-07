<?php

namespace Modules\System\Providers;

use Modules\System\Models\Option;

use Nova\Module\Support\Providers\ModuleServiceProvider as ServiceProvider;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Config;


class ModuleServiceProvider extends ServiceProvider
{
    /**
     * The additional provider class names.
     *
     * @var array
     */
    protected $providers = array(
        'Modules\System\Providers\AuthServiceProvider',
        'Modules\System\Providers\EventServiceProvider',
        'Modules\System\Providers\RouteServiceProvider',
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
        $this->package('Modules/System', 'system', $path);

        // Bootstrap the Package.
        $path = $path .DS .'Bootstrap.php';

        $this->bootstrapFrom($path);

        // Load the Options from database.
        $this->loadOptions();
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

        //
    }

    /**
     * Load the options from database.
     *
     * @return void
     */
    protected function loadOptions()
    {
        try {
            // Retrieve the Option items, caching them for 24 hours.
            $options = Cache::remember('system_options', 1440, function()
            {
                return Option::all();
            });
        }
        catch (\Exception $e) {
            $options = array();
        }

        // Setup the information stored on the Option instances into Configuration.
        foreach ($options as $option) {
            if (! empty($option->namespace)) {
                $key =  sprintf('%s::%s.%s', $option->namespace, $option->group, $option->item);
            } else {
                $key =  sprintf('%s.%s', $option->group, $option->item);
            }

            Config::set($key, $option->value);
        }
    }

}
