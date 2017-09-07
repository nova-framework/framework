<?php

namespace App\Providers;

use Nova\Routing\Router;
use Nova\Support\Facades\Broadcast;
use Nova\Support\ServiceProvider;


class BroadcastServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadBroadcastRoutes($router);

        $this->loadBroadcastChannels();
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
     *
     * @param Nova\Routing\Router $router
     */
    protected function loadBroadcastRoutes(Router $router)
    {
        $router->group(array('middleware' => 'web'), function ($router)
        {
            $router->post('broadcasting/auth', function (Request $request)
            {
                return Broadcast::authenticate($request);
            });
        });
    }

    /**
     * Load the Broadcasting Channels.
     */
    protected function loadBroadcastChannels()
    {
        $path = app_path('Broadcast.php');

        if (is_readable($path)) require $path;
    }
}

