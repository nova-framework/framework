<?php
/**
 * RoutingServiceProvider - Implements a Service Provider for Routing.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Nova\Routing;

use Nova\Config\Config;
use Nova\Routing\Router;
use Nova\Routing\Redirector;
use Nova\Routing\UrlGenerator;
use Nova\Support\ServiceProvider;


class RoutingServiceProvider extends ServiceProvider
{
    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAssetsDispatcher();

        $this->registerRouter();

        $this->registerUrlGenerator();

        $this->registerRedirector();
    }

    /**
     * Register the Assets Dispatcher.
     */
    public function registerAssetsDispatcher()
    {
        // NOTE: When this method is executed, the Config Store is not yet available.
        $driver = Config::get('routing.assets.driver', 'default');

        if ($driver == 'custom') {
            $className = Config::get('routing.assets.dispatcher');
        } else {
            $className = 'Nova\Routing\Assets\\' .ucfirst($driver) .'Dispatcher';
        }

        // Bind the calculated class name to the Assets Dispatcher Interface.
        $this->app->bind('Nova\Routing\Assets\DispatcherInterface', $className);
    }

    /**
     * Register the Router instance.
     *
     * @return void
     */
    protected function registerRouter()
    {
        $this->app['router'] = $this->app->share(function($app)
        {
            return new Router($app['events'], $app);
        });
    }

    /**
     * Register the URL generator service.
     *
     * @return void
     */
    protected function registerUrlGenerator()
    {
        $this->app['url'] = $this->app->share(function($app)
        {
            // The URL Generator needs the Route Collection that exists on the Router.
            $routes = $app['router']->getRoutes();

            return new UrlGenerator($routes, $app->rebinding('request', function($app, $request)
            {
                $app['url']->setRequest($request);
            }));
        });
    }

    /**
     * Register the Redirector service.
     *
     * @return void
     */
    protected function registerRedirector()
    {
        $this->app['redirect'] = $this->app->share(function($app)
        {
            $redirector = new Redirector($app['url']);

            if (isset($app['session.store'])) {
                $redirector->setSession($app['session.store']);
            }

            return $redirector;
        });
    }

}
