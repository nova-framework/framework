<?php

namespace App\Providers;

use Nova\Support\Facades\Broadcast;
use Nova\Support\ServiceProvider;


class BroadcastServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutes();

        //
        require app_path('Channels.php');
    }

    /**
     * Register the Application's Service Provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Load the Broadcasting Routes.
     */
    protected function loadRoutes()
    {
        $this->app['router']->group(array('middleware' => 'web'), function ($router)
        {
            $router->post('broadcasting/auth', function (Request $request)
            {
                return Broadcast::authenticate($request);
            });
        });
    }
}

