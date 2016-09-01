<?php

namespace Events;

use Support\ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['events'] = $this->app->share(function($app)
        {
            return new Dispatcher($app);
        });
    }

}
