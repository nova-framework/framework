<?php
/**
 * PaginationServiceProvider - Implements a Service Provider for Pagination.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Pagination;

use Validation\Factory;
use Support\ServiceProvider;


class PaginationServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the Provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    
    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('paginator', function($app)
        {
            $paginator = new Factory($app['request'], $app['view'], $app['translator']);

            $app->refresh('request', $paginator, 'setRequest');

            return $paginator;
        });
    }

    /**
     * Get the services provided by the Provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('paginator');
    }

}
