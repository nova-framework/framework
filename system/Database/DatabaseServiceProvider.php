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
use Database\Model as BasicModel;
use Helpers\Database as DatabaseHelper;
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
        $db = $this->app['db'];

        $events = $this->app['events'];

        // Setup the ORM Model.
        Model::setConnectionResolver($db);

        Model::setEventDispatcher($events);

        // Setup the (basic) Model.
        BasicModel::setConnectionResolver($db);

        // Setup the legacy Database Helper.
        DatabaseHelper::setConnectionResolver($db);
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
