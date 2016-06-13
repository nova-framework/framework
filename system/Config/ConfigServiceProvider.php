<?php
/**
 * DatabaseServiceProvider - Implements a Service Provider for Database.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database;

use Config\DatabaseLoader;
use Config\LoaderManager;
use Config\Repository;
use Support\Facades\Facade;
use Support\ServiceProvider;


class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('config', function($app)
        {
            // Get a LoaderManager instance
            $loader = $app['config.loader'];

            return new Repository($loader);
        });

        Facade::clearResolvedInstance('config');
    }

    /**
     * Register the Database Presence Verifier.
     *
     * @return void
     */
    protected function registerLoader()
    {
        $this->app->bindShared('config.loader', function($app)
        {
            // Get a LoaderManager instance
            $loader = new LoaderManager();

            if(APPCONFIG_STORE == 'database') {
                // Get a Database Connection instance.
                $connection = $app['db']->connection();

                $loader->setConnection($connection);
            } else if(APPCONFIG_STORE != 'files') {
                throw new \InvalidArgumentException('Invalid Config Store type.');
            }

            return $loader;
        });
    }

}
