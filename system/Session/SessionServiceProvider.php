<?php
/**
 * SessionServiceProvider - Implements a Service Provider for Session.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Session;

use Session\DatabaseSessionHandler;
use Session\FileSessionHandler;
use Session\Store;
use Support\ServiceProvider;


class SessionServiceProvider extends ServiceProvider
{
    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $me = $this;

        $this->app->bindShared('session.store', function($app) use ($me)
        {
            $config = $app['config']['session'];

            $cookie = $config['cookie'];

            // Retrieve the CookieJar instance.
            $request = $app['request'];

            $token = $request->cookie($cookie);

            // Register the Session Handler.
            $me->registerSessionHandler($config);

            $me->startSession($config);

            $store = new Store($cookie, $app['session.handler'], $token);

            return $store->start();
        });
    }

    /**
     * Register session driver.
     *
     * @param  array  $config
     * @return void
     */
    public function registerSessionHandler(array $config)
    {
        $me = $this;

        $this->app->bindShared('session.handler', function($app) use ($me, $config)
        {
            $lifeTime = (int) $config['lifetime'] * 60;

            $driver = array_get($config, 'driver', 'file');

            switch ($driver) {
                case 'database':
                    return new DatabaseSessionHandler($app['db']->connection(), $config);
                break;

                case 'file':
                    return new FileSessionHandler($config);
                break;
            }
        });
    }

    protected function startSession(array $config)
    {
        $cookieJar = $this->app['cookie'];

        // Start the Session.
        $lifeTime = (int) $config['lifetime'] * 60;

        session_set_cookie_params($lifeTime, $config['path'], $config['domain']);

        session_start();

        // Create and queue a Cookie containing the proper Session's lifetime.
        $cookie = $cookieJar->make(
            session_name(),
            session_id(),
            $config['lifetime'],
            $config['path'],
            $config['domain'],
            $config['secure'],
            false
        );

        $cookieJar->queue($cookie);
    }
}

