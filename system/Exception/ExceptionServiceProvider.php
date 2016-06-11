<?php
/**
 * ExceptionServiceProvider - Implements a Service Provider for the Exception Handler.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Exception;

use Support\ServiceProvider;


class ExceptionServiceProvider extends ServiceProvider
{
    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerDisplayer();

        $this->registerHandler();
    }

    /**
     * Register the Exception Handler instance.
     *
     * @return void
     */
    protected function registerHandler()
    {
        $this->app['exception'] = $this->app->share(function($app)
        {
            return new Handler($app, $app['exception.displayer']);
        });
    }

    /**
     * Register the Exception Displayer.
     *
     * @return void
     */
    protected function registerDisplayer()
    {
        $this->app['exception.displayer'] = $this->app->share(function($app)
        {
            return new ExceptionDisplayer();
        });
    }
}
