<?php

namespace App\Modules\System\Providers;

use App\Modules\System\Models\Option;

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
        //
    }

    /**
     * Load the options from database.
     *
     * @return void
     */
    protected function loadOptions()
    {
        $options = Option::remember(1440, 'system_options')->get();

        foreach ($options as $option) {
            $key = $option->group .'.' .$option->item;

            Config::set($key, $option->value);
        }
    }

}
