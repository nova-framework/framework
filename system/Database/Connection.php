<?php
/**
 * Connection - A PDO based Database Connection.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database;

use Core\Config;
use Core\Logger;
use Database\Connectors\Connector;
use Database\Connectors\MySqlConnector;
use Database\Connectors\PostgresConnector;
use Database\Connectors\SQLiteConnector;
use Database\Query\Expression;
use Database\Query\Builder as QueryBuilder;
use Database\QueryException;

use Closure;
use PDO;
use DateTimeInterface;


class Connection
{
    /**
     * Connection instances
     *
     * @var Connection[]
     */
    private static $instances = array();

    /**
     * The Connector instance.
     *
     * @var
     */
    protected $connector;

    /**
     * The active PDO Connection.
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * The default fetch mode of the Connection.
     *
     * @var int
     */
    protected $fetchMode = PDO::FETCH_OBJ;

    /**
     * The number of active transactions.
     *
     * @var int
     */
    protected $transactions = 0;

    /**
     * All of the queries run against the connection.
     *
     * @var array
     */
    protected $queryLog = array();

    /**
     * Indicates whether queries are being logged.
     *
     * @var bool
     */
    protected $loggingQueries = true;

    /**
     * Indicates if the connection is in a "dry run".
     *
     * @var bool
     */
    protected $pretending = false;

    /**
     * The name of the connected Database.
     *
     * @var string
     */
    protected $database;

    /**
     * The table prefix for the Connection.
     *
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * The database connection configuration options.
     *
     * @var array
     */
    protected $config = array();

    /**
     * Create a new Connection instance.
     *
     * @param  array  $config
     * @return void
     */
    public function __construct(array $config)
    {
        $this->database = $config['dbname'];

        $this->tablePrefix = $config['prefix'];

        $this->config = $config;

        //
        $this->connector = $this->createConnector($config);

        $this->pdo = $this->createConnection($config);
    }

    /**
     * Retrieve an instance of the Connection.
     *
     * @param $name string|array Name of the Connection provided in the configuration or options array
     * @return \Database\Connection|null
     * @throws \Exception
     */
    public static function getInstance($name = null)
    {
        $name = $name ?: Config::get('database.default');

        // If there is already a Connection instantiated, return it.
        if (isset(static::$instances[$name])) {
            return static::$instances[$name];
        }

        // Retrieve the requested configuration.
        $connections = Config::get('database.connections');

        if (is_null($config = array_get($connections, $name))) {
            throw new \InvalidArgumentException("Database [$name] not configured.");
        }

        // Create the Connection instance.
        static::$instances[$name] = $connection = new static($config);

        // Setup the Fetch Mode on Connection instance and return it.
        $mode = Config::get('database.fetch');

        return $connection->setFetchMode($mode);
    }

    /**
     * Return the current Connection instance.
     *
     * @return \Database\Connection
     */
    public function connection()
    {
        return $this;
    }

    /**
     * Create a connector instance based on the configuration.
     *
     * @param  array  $config
     * @return \Database\Connectors\ConnectorInterface
     *
     * @throws \InvalidArgumentException
     */
    public function createConnector(array $config)
    {
        if (! isset($config['driver'])) {
            throw new \InvalidArgumentException("A driver must be specified.");
        }

        switch ($config['driver']) {
            case 'mysql':
                return new MySqlConnector();

            case 'pgsql':
                return new PostgresConnector();

            case 'sqlite':
                return new SQLiteConnector();
        }

        throw new \InvalidArgumentException("Unsupported driver [{$config['driver']}]");
    }

    /**
     * Create a new PDO connection.
     *
     * @param  array   $config
     * @return PDO
     */
    public function createConnection(array $config)
    {
        return $this->connector->connect($config);
    }

    /**
     * Begin a Fluent Query against a database table.
     *
     * @param  string  $table
     * @return \Database\Query\Builder
     */
    public function table($table)
    {
        $query = new QueryBuilder($this);

        return $query->from($table);
    }

    /**
     * Get a new raw query expression.
     *
     * @param  mixed  $value
     * @return \Database\Query\Expression
     */
    public function raw($value)
    {
        return new Expression($value);
    }

    /**
     * Run a select statement and return a single result.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return mixed
     */
    public function selectOne($query, $bindings = array())
    {
        $records = $this->select($query, $bindings);

        return (count($records) > 0) ? reset($records) : null;
    }

    /**
     * Run a select statement against the database.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return array
     */
    public function select($query, array $bindings = array())
    {
        return $this->run($query, $bindings, function($me, $query, $bindings)
        {
            if ($me->pretending()) return array();

            //
            $statement = $me->getPdo()->prepare($query);

            $bindings = $this->prepareBindings($bindings);

            $statement->execute($bindings);

            return $statement->fetchAll($me->getFetchMode());
        });
    }

    /**
     * Run an insert statement against the database.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return bool
     */
    public function insert($query, array $bindings = array())
    {
        return $this->statement($query, $bindings);
    }

    /**
     * Run an update statement against the database.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return int
     */
    public function update($query, array $bindings = array())
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Run a delete statement against the database.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return int
     */
    public function delete($query, array $bindings = array())
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Execute an SQL statement and return the boolean result.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return bool
     */
    public function statement($query, array $bindings = array())
    {
        return $this->run($query, $bindings, function($me, $query, $bindings)
        {
            if ($me->pretending()) return true;

            //
            $statement = $me->getPdo()->prepare($query);

            $bindings = $me->prepareBindings($bindings);

            return $statement->execute($bindings);
        });
    }

    /**
     * Run an SQL statement and get the number of rows affected.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return int
     */
    public function affectingStatement($query, array $bindings = array())
    {
        return $this->run($query, $bindings, function($me, $query, $bindings)
        {
            if ($me->pretending()) return 0;

            //
            $statement = $me->getPdo()->prepare($query);

            $bindings = $me->prepareBindings($bindings);

            $statement->execute($bindings);

            return $statement->rowCount();
        });
    }

    /**
     * Run a raw, unprepared query against the PDO connection.
     *
     * @param  string  $query
     * @return bool
     */
    public function unprepared($query)
    {
        return $this->run($query, array(), function($me, $query, $bindings)
        {
            if ($me->pretending()) return true;

            return (bool) $me->getPdo()->exec($query);
        });
    }

    /**
     * Prepare the query bindings for execution.
     *
     * @param  array  $bindings
     * @return array
     */
    public function prepareBindings(array $bindings)
    {
        foreach ($bindings as $key => $value) {
            if ($value instanceof DateTimeInterface) {
                // We need to transform all DateTime instances into an actual date string.
                $bindings[$key] = $value->format($this->getDateFormat());
            } else if ($value === false) {
                $bindings[$key] = 0;
            }
        }

        return $bindings;
    }

    /**
     * Execute a Closure within a transaction.
     *
     * @param  Closure  $callback
     * @return mixed
     *
     * @throws \Exception
     */
    public function transaction(Closure $callback)
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);

            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();

            throw $e;
        }

        return $result;
    }

    /**
     * Start a new database transaction.
     *
     * @return void
     */
    public function beginTransaction()
    {
        ++$this->transactions;

        if ($this->transactions == 1) {
            $this->pdo->beginTransaction();
        }
    }

    /**
     * Commit the active database transaction.
     *
     * @return void
     */
    public function commit()
    {
        if ($this->transactions == 1) $this->pdo->commit();

        --$this->transactions;
    }

    /**
     * Rollback the active database transaction.
     *
     * @return void
     */
    public function rollBack()
    {
        if ($this->transactions == 1) {
            $this->transactions = 0;

            $this->pdo->rollBack();
        } else {
            --$this->transactions;
        }
    }

    /**
     * Get the number of active transactions.
     *
     * @return int
     */
    public function transactionLevel()
    {
        return $this->transactions;
    }

    /**
     * Execute the given callback in "dry run" mode.
     *
     * @param  \Closure  $callback
     * @return array
     */
    public function pretend(Closure $callback)
    {
        $this->pretending = true;

        $this->queryLog = array();

        // Execute the Callback in the pretend mode.
        $callback($this);

        $this->pretending = false;

        return $this->queryLog;
    }

    /**
     * Run a SQL statement and log its execution context.
     *
     * @param  string    $query
     * @param  array     $bindings
     * @param  \Closure  $callback
     * @return mixed
     *
     * @throws \Database\QueryException
     */
    protected function run($query, $bindings, Closure $callback)
    {
        $start = microtime(true);

        $result = $this->runQueryCallback($query, $bindings, $callback);

        $time = $this->getElapsedTime($start);

        $this->logQuery($query, $bindings, $time);

        return $result;
    }

    /**
     * Run a SQL statement.
     *
     * @param  string    $query
     * @param  array     $bindings
     * @param  \Closure  $callback
     * @return mixed
     *
     * @throws \Database\QueryException
     */
    protected function runQueryCallback($query, $bindings, Closure $callback)
    {
        try {
            $result = $callback($this, $query, $bindings);
        } catch (\Exception $e) {
            throw new QueryException($query, $this->prepareBindings($bindings), $e);
        }

        return $result;
    }

    /**
     * Log a query in the connection's query log.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @param  float|null  $time
     * @return void
     */
    public function logQuery($query, $bindings, $time = null)
    {
        if (! $this->loggingQueries) return;

        $this->queryLog[] = compact('query', 'bindings', 'time');
    }

    /**
     * Get the elapsed time since a given starting point.
     *
     * @param  int    $start
     * @return float
     */
    protected function getElapsedTime($start)
    {
        return round((microtime(true) - $start) * 1000, 2);
    }

    /**
     * Get the current configuration for the Connection.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the name of the connected Database.
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->database;
    }

    /**
     * Get the table prefix for the Connection.
     *
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * Set the table prefix in use by the Connection.
     *
     * @param  string  $prefix
     * @return void
     */
    public function setTablePrefix($prefix)
    {
        $this->tablePrefix = $prefix;
    }

    /**
     * Get the Database Driver.
     *
     * @return string
     */
    public function getDriver()
    {
        return isset($this->config['driver']) ? $this->config['driver'] : null;
    }

    /**
     * Get the Connector instance.
     *
     * @return \Database\Connectors\Connector
     */
    public function getConnector()
    {
        return $this->connector;
    }

    /**
     * Get the PDO instance.
     *
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Set the PDO connection.
     *
     * @param  \PDO|null  $pdo
     * @return $this
     *
     * @throws \RuntimeException
     */
    public function setPdo($pdo)
    {
        if ($this->transactions >= 1) {
            throw new \RuntimeException("Can't swap PDO instance while within transaction.");
        }

        $this->pdo = $pdo;

        return $this;
    }

    /**
     * Determine if the connection in a "dry run".
     *
     * @return bool
     */
    public function pretending()
    {
        return ($this->pretending === true);
    }

    /**
     * Get the default fetch mode for the Connection.
     *
     * @return int
     */
    public function getFetchMode()
    {
        return $this->fetchMode;
    }

    /**
     * Set the default fetch mode for the Connection.
     *
     * @param  int  $fetchMode
     * @return \Database\Connection
     */
    public function setFetchMode($fetchMode)
    {
        $this->fetchMode = $fetchMode;

        return $this;
    }

    /**
     * Get the connection query log.
     *
     * @return array
     */
    public function getQueryLog()
    {
        return $this->queryLog;
    }

    /**
     * Clear the query log.
     *
     * @return void
     */
    public function flushQueryLog()
    {
        $this->queryLog = array();
    }

    /**
     * Enable the query log on the connection.
     *
     * @return void
     */
    public function enableQueryLog()
    {
        $this->loggingQueries = true;
    }

    /**
     * Disable the query log on the connection.
     *
     * @return void
     */
    public function disableQueryLog()
    {
        $this->loggingQueries = false;
    }

    /**
     * Determine whether we're logging queries.
     *
     * @return bool
     */
    public function logging()
    {
        return $this->loggingQueries;
    }

    /**
     * Get the keyword identifier wrapper format.
     *
     * @return string
     */
    public function getWrapper()
    {
        return $this->connector->getWrapper();
    }

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->connector->getDateFormat();
    }
}
