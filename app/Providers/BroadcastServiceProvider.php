<?php

namespace App\Providers;

use Nova\Http\Request;
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
        $router->post('broadcasting/auth', array('middleware' => 'web', function (Request $request)
        {
            return Broadcast::authenticate($request);
        }));

        $this->loadBroadcastChannels();
    }

    /**
     * Load the Broadcast Channels.
     */
    protected function loadBroadcastChannels()
    {
        require app_path('Routes/Channels.php');
    }
}
