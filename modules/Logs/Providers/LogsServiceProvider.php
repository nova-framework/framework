<?php

namespace Modules\Logs\Providers;

use Nova\Support\ServiceProvider;

use Modules\Logs\Observers\UserActionsObserver;

use Modules\System\Models\Role;
use Modules\Users\Models\User;


class LogsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        $observer = new UserActionsObserver();

        //
        User::observe($observer);
        Role::observe($observer);
    }

    /**
     * Register the Logs module Service Provider.
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
        //$this->app->register('Modules\Logs\Providers\AuthServiceProvider');
        //$this->app->register('Modules\Logs\Providers\EventServiceProvider');
    }

}
