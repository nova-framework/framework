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
        $this->loadRoutes($router);

        $this->loadChannels();
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
    protected function loadRoutes(Router $router)
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
    protected function loadChannels()
    {
        require app_path('Channels.php');
    }
}

