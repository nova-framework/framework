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
        $this->app['exception'] = $this->app->share(function($app)
        {
            return new Handler();
        });
    }
}
