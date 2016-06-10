<?php

namespace Database;

use Database\Connection;


class DatabaseManager implements ConnectionResolverInterface
{
    /**
     * The Application instance.
     *
     * @var \Core\Application
     */
    protected $app;

    /**
     * The active Connection instances.
     *
     * @var array
     */
    protected $connections = array();


    /**
     * Create a new Database Manager instance.
     *
     * @param  \core\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a database Connection instance.
     *
     * @param  string  $name
     * @return \Database\Connection
     */
    public function connection($name = null)
    {
        $name = $name ?: $this->getDefaultConnection();

        if ( ! isset($this->connections[$name])) {
            $connection = $this->makeConnection($name);

            $this->connections[$name] = $this->prepare($connection);
        }

        return $this->connections[$name];
    }

    /**
     * Reconnect to the given database.
     *
     * @param  string  $name
     * @return \Database\Connection
     */
    public function reconnect($name = null)
    {
        $name = $name ?: $this->getDefaultConnection();

        $this->disconnect($name);

        return $this->connection($name);
    }

    /**
     * Disconnect from the given database.
     *
     * @param  string  $name
     * @return void
     */
    public function disconnect($name = null)
    {
        $name = $name ?: $this->getDefaultConnection();

        unset($this->connections[$name]);
    }

    /**
     * Make the database connection instance.
     *
     * @param  string  $name
     * @return \Database\Connection
     */
    protected function makeConnection($name)
    {
        $config = $this->getConfig($name);

        return new Connection($config);
    }

    /**
     * Prepare the database connection instance.
     *
     * @param  \Database\Connection  $connection
     * @return \Database\Connection
     */
    protected function prepare(Connection $connection)
    {
        $connection->setFetchMode($this->app['config']['database.fetch']);

        if ($this->app->bound('events')) {
            $connection->setEventDispatcher($this->app['events']);
        }

        $app = $this->app;

        // Setup the Cache.
        $connection->setCacheManager(function() use ($app)
        {
            return $app['cache'];
        });


        // Setup the Paginator.
        $connection->setPaginator(function() use ($app)
        {
            return $app['paginator'];
        });

        return $connection;
    }

    /**
     * Get the configuration for a Connection.
     *
     * @param  string  $name
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function getConfig($name)
    {
        $name = $name ?: $this->getDefaultConnection();

        $connections = $this->app['config']['database.connections'];

        if (is_null($config = array_get($connections, $name))) {
            throw new \InvalidArgumentException("Database [$name] not configured.");
        }

        return $config;
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return $this->app['config']['database.default'];
    }

    /**
     * Set the default connection name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultConnection($name)
    {
        $this->app['config']['database.default'] = $name;
    }

    /**
     * Register an extension connection resolver.
     *
     * @param  string    $name
     * @param  callable  $resolver
     * @return void
     */
    public function extend($name, $resolver)
    {
        $this->extensions[$name] = $resolver;
    }

    /**
     * Return all of the created connections.
     *
     * @return array
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->connection(), $method), $parameters);
    }

}
