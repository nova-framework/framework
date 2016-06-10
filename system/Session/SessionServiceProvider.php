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
                    $handler = new DatabaseSessionHandler($app['db'], $config['table'], $lifeTime);
                break;

                case 'file':
                    $handler = new FileSessionHandler($config, $lifeTime);
                break;
            }

            // Setup the Save Session Handler.
            session_set_save_handler($handler, true);

            // The following prevents unexpected effects when using objects as save handlers
            register_shutdown_function('session_write_close');

            return $handler;
        });
    }
}

