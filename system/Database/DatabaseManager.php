<?php

namespace Database;

use Support\Str;
use Database\ConnectionFactory;


class DatabaseManager implements ConnectionResolverInterface
{
    /**
     * The application instance.
     *
     * @var \Foundation\Application
     */
    protected $app;

    /**
     * The database connection factory instance.
     *
     * @var \Database\ConnectionFactory
     */
    protected $factory;

    /**
     * The active connection instances.
     *
     * @var array
     */
    protected $connections = array();

    /**
     * The custom connection resolvers.
     *
     * @var array
     */
    protected $extensions = array();

    /**
     * Create a new database manager instance.
     *
     * @param  \Foundation\Application  $app
     * @param  \Database\ConnectionFactory  $factory
     * @return void
     */
    public function __construct($app, ConnectionFactory $factory)
    {
        $this->app = $app;
        $this->factory = $factory;
    }

    /**
     * Get a database connection instance.
     *
     * @param  string  $name
     * @return \Database\Connection
     */
    public function connection($name = null)
    {
        list($name, $type) = $this->parseConnectionName($name);

        if ( ! isset($this->connections[$name])) {
            $connection = $this->makeConnection($name);

            $this->setPdoForType($connection, $type);

            $this->connections[$name] = $this->prepare($connection);
        }

        return $this->connections[$name];
    }

    /**
     * Parse the connection into an array of the name and read / write type.
     *
     * @param  string  $name
     * @return array
     */
    protected function parseConnectionName($name)
    {
        $name = $name ?: $this->getDefaultConnection();

        return Str::endsWith($name, ['::read', '::write']) ? explode('::', $name, 2) : [$name, null];
    }

    /**
     * Disconnect from the given database and remove from local cache.
     *
     * @param  string  $name
     * @return void
     */
    public function purge($name = null)
    {
        $this->disconnect($name);

        unset($this->connections[$name]);
    }

    /**
     * Disconnect from the given database.
     *
     * @param  string  $name
     * @return void
     */
    public function disconnect($name = null)
    {
        if (isset($this->connections[$name = $name ?: $this->getDefaultConnection()])) {
            $this->connections[$name]->disconnect();
        }
    }

    /**
     * Reconnect to the given database.
     *
     * @param  string  $name
     * @return \Database\Connection
     */
    public function reconnect($name = null)
    {
        $this->disconnect($name = $name ?: $this->getDefaultConnection());

        if ( ! isset($this->connections[$name])) {
            return $this->connection($name);
        }

        return $this->refreshPdoConnections($name);
    }

    /**
     * Refresh the PDO connections on a given connection.
     *
     * @param  string  $name
     * @return \Database\Connection
     */
    protected function refreshPdoConnections($name)
    {
        $fresh = $this->makeConnection($name);

        return $this->connections[$name]
            ->setPdo($fresh->getPdo())
            ->setReadPdo($fresh->getReadPdo());
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

        if (isset($this->extensions[$name])) {
            return call_user_func($this->extensions[$name], $config, $name);
        }

        $driver = $config['driver'];

        if (isset($this->extensions[$driver])) {
            return call_user_func($this->extensions[$driver], $config, $name);
        }

        return $this->factory->make($config, $name);
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

        $connection->setCacheManager(function() use ($app)
        {
            return $app['cache'];
        });

        $connection->setPaginator(function() use ($app)
        {
            return $app['paginator'];
        });

        $connection->setReconnector(function($connection)
        {
            $this->reconnect($connection->getName());
        });

        return $connection;
    }

    /**
     * Prepare the read write mode for database connection instance.
     *
     * @param  \Database\Connection  $connection
     * @param  string  $type
     * @return \Database\Connection
     */
    protected function setPdoForType(Connection $connection, $type = null)
    {
        if ($type == 'read') {
            $connection->setPdo($connection->getReadPdo());
        } else if ($type == 'write') {
            $connection->setReadPdo($connection->getPdo());
        }

        return $connection;
    }

    /**
     * Get the configuration for a connection.
     *
     * @param  string  $name
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function getConfig($name)
    {
        $name = $name ?: $this->getDefaultConnection();

        //
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
    public function extend($name, callable $resolver)
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
