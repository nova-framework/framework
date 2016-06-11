<?php
/**
 * DatabaseServiceProvider - Implements a Service Provider for Database.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database;

use Database\DatabaseManager;
use Database\ORM\Model;
use Support\ServiceProvider;


class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the Application events.
     *
     * @return void
     */
    public function boot()
    {
        //Model::setConnectionResolver($this->app['db']);

        Model::setEventDispatcher($this->app['events']);
    }

    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('db', function($app)
        {
            return new DatabaseManager($app);
        });
    }

}
