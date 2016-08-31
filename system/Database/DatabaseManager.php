<?php

namespace Database;

use Database\Connectors\MySqlConnector;
use Database\Connectors\PostgresConnector;
use Database\Connectors\SQLiteConnector;
use Database\Connectors\SqlServerConnector;

use Database\Query\Grammars\MySqlGrammar;
use Database\Query\Grammars\PostgresGrammar;
use Database\Query\Grammars\SQLiteGrammar;
use Database\Query\Grammars\SqlServerGrammar;

use Database\Query\Processors\MySqlProcessor;
use Database\Query\Processors\PostgresProcessor;
use Database\Query\Processors\SQLiteProcessor;
use Database\Query\Processors\SqlServerProcessor;

use Database\Connection;


class DatabaseManager implements ConnectionResolverInterface
{
    /**
     * The Application instance.
     *
     * @var \Foundation\Application
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

        if (! isset($this->connections[$name])) {
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
     *
     * @throws \InvalidArgumentException
     */
    protected function makeConnection($name)
    {
        $config = $this->getConfig($name);

        if (! isset($config['driver'])) {
            throw new \InvalidArgumentException("A driver must be specified.");
        }

        $driver = $config['driver'];

        return new Connection(
            $config['database'],
            $config['prefix'],
            $config,
            $this->createConnector($driver),
            $this->createQueryGrammar($driver),
            $this->createQueryProcessor($driver)
        );
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
     * Create a connector instance based on the configuration.
     *
     * @param  array  $config
     * @return \Database\ConnectorInterface
     *
     * @throws \InvalidArgumentException
     */
    public function createConnector($driver)
    {
        switch ($driver) {
            case 'mysql':
                return new MySqlConnector();

            case 'pgsql':
                return new PostgresConnector();

            case 'sqlite':
                return new SQLiteConnector();

            case 'sqlsrv':
                return new SqlServerConnector();
        }

        throw new \InvalidArgumentException("Unsupported driver [$driver]");
    }

    /**
     * Create a Query Grammar instance based on the configuration.
     *
     * @param  array  $config
     * @return \Database\Query\Grammar
     *
     * @throws \InvalidArgumentException
     */
    public function createQueryGrammar($driver)
    {
        switch ($driver) {
            case 'mysql':
                return new MySqlGrammar();

            case 'pgsql':
                return new PostgresGrammar();

            case 'sqlite':
                return new SQLiteGrammar();

            case 'sqlsrv':
                return new SqlServerGrammar();
        }

        throw new \InvalidArgumentException("Unsupported driver [$driver]");
    }

    /**
     * Create a Query Processor instance based on the configuration.
     *
     * @param  array  $config
     * @return \Database\Query\Processor
     *
     * @throws \InvalidArgumentException
     */
    public function createQueryProcessor($driver)
    {
        switch ($driver) {
            case 'mysql':
                return new MySqlProcessor();

            case 'pgsql':
                return new PostgresProcessor();

            case 'sqlite':
                return new SQLiteProcessor();

            case 'sqlsrv':
                return new SqlServerProcessor();
        }

        throw new \InvalidArgumentException("Unsupported driver [$driver]");
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
