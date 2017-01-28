<?php

namespace Modules\System\Providers;

use Modules\System\Models\Option;

use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Config;
use Nova\Support\ServiceProvider;


class SystemServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
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
        // Register additional Service Providers.
        //$this->app->register('Modules\System\Providers\AuthServiceProvider');
        //$this->app->register('Modules\System\Providers\EventServiceProvider');
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

        // Setup the information stored on the Option instances.
        foreach ($options as $option) {
            $key =  sprintf('%s.%s', $option->group, $option->item);

            Config::set($key, $option->value);
        }
    }

}
