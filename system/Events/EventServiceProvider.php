<?php
/**
 * EventServiceProvider - Implements a Service Provider for Events Dispatcher.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Events;

use Support\ServiceProvider;
use Events\Dispatcher;


class EventServiceProvider extends ServiceProvider
{
    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['events'] = $this->app->share(function($app) {
            return new Dispatcher($app);
        });
    }
}
