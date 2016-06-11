<?php
/**
 * ExceptionServiceProvider - Implements a Service Provider for the Exception Handler.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Exception;

use Exception\Handler;
use Exception\HttpExceptionDisplayer;
use Exception\JsonExceptionDisplayer;

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
        $this->registerHttpDisplayer();
        $this->registerJsonDisplayer();

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
            $config = $app['config'];

            return new Handler(
                $app,
                $app['exception.httpDisplayer'],
                $app['exception.httpDisplayer'],
                $config['debug']
            );
        });
    }

    /**
     * Register the Http Exception Displayer.
     *
     * @return void
     */
    protected function registerHttpDisplayer()
    {
        $this->app['exception.httpDisplayer'] = $this->app->share(function($app)
        {
            return new HttpExceptionDisplayer();
        });
    }

    /**
     * Register the Json Exception Displayer.
     *
     * @return void
     */
    protected function registerJsonDisplayer()
    {
        $this->app['exception.jsonDisplayer'] = $this->app->share(function($app)
        {
            return new JsonExceptionDisplayer();
        });
    }
}
