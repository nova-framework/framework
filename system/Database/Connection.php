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
use Database\Query\Expression;
use Database\Query\Builder as QueryBuilder;

use \PDO;
use \DateTime;


class Connection
{
    /**
     * Connection instances
     *
     * @var Connection[]
     */
    private static $instances = array();

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
        $this->pdo = $this->createConnection($config);

        $this->database = $config['dbname'];

        $this->tablePrefix = $config['prefix'];

        $this->config = $config;
    }

    /**
     * Retrieve an instance of the Connection.
     *
     * @param $name string|array Name of the Connection provided in the configuration or options array
     * @return \Database\Connection|null
     * @throws \Exception
     */
    public static function getInstance($config = 'default')
    {
        if (is_array($config) && ! empty($config)) {
            // The given parameter is a configuration array.
            $connection = implode('.', array_values($config));
        } else {
            $connection = (is_string($config) && ! empty($config)) ? $config : 'default';

            // Retrieve the configuration with the specified name.
            $config = Config::get('database');

            if (isset($config[$connection]) && ! empty($config[$connection])) {
                $config = $config[$connection];
            } else {
                throw new \Exception("Connection name '$connection' is not defined in your configuration");
            }
        }

        // Prepare a Token for handling the Connection instances.
        $token = md5($connection);

        // If there is already a Connection instantiated, return it.
        if (isset(static::$instances[$token])) {
            return static::$instances[$token];
        }

        // Create the Connection instance and return it.
        return static::$instances[$token] = new static($config);
    }

    /**
     * Create a new PDO connection.
     *
     * @param  array   $config
     * @return PDO
     */
    public function createConnection(array $config)
    {
        extract($config);

        $dsn = "$driver:host={$hostname};dbname={$database}";

        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE {$collation}"
        );

        return new PDO($dsn, $username, $password, $options);
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
        $statement = $this->getPdo()->prepare($query);

        $bindings = $this->prepareBindings($bindings);

        // Execute the Statement.
        $statement->execute($bindings);

        return $statement->fetchAll($this->getFetchMode());
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
        $statement = $this->getPdo()->prepare($query);

        $bindings = $this->prepareBindings($bindings);

        // Execute the Statement and return the result.
        return $statement->execute($bindings);
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
        $statement = $this->getPdo()->prepare($query);

        $bindings = $this->prepareBindings($bindings);

        // Execute the Statement.
        $statement->execute($bindings);

        return $statement->rowCount();
    }

    /**
     * Run a raw, unprepared query against the PDO connection.
     *
     * @param  string  $query
     * @return bool
     */
    public function unprepared($query)
    {
        return (bool) $this->getPdo()->exec($query);
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
            if ($value instanceof DateTime) {
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
     * Get the PDO instance.
     *
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
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
     * Get the format for database stored dates.
     *
     * @return string
     */
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s';
    }
}
