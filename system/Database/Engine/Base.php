<?php
/**
 * MySQL Engine.
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date December 19th, 2015
 */

namespace Nova\Database\Engine;

use Nova\Database\Engine;
use Nova\Database\Manager;


abstract class Base extends \PDO implements Engine
{
    /** @var int PDO Fetch method. */
    protected $method = \PDO::FETCH_OBJ;

    /** @var array Config from the user's app config. */
    protected $config;

    /** @var int Counting how much queries have been executed in total. */
    protected $queryCount;

    /**
     * MySQLEngine constructor.
     * Please use the Factory to maintain instances of the drivers.
     *
     * @param $config array
     *
     * @throws \PDOException
     */
    public function __construct($dsn, $config = array(), $options = array()) {
        // Check for valid Config.
        if (! is_array($config) || ! is_array($options)) {
            throw new \UnexpectedValueException('Config and Options parameters should be Arrays');
        }

        // Will set the default method when provided in the config.
        if (isset($config['fetch_method'])) {
            $this->method = $config['fetch_method'];
        }

        // Reset the query counter
        $this->queryCount = 0;

        // Store the config in class variable.
        $this->config = $config;

        //
        $username = isset($config['username']) ? $config['username'] : '';
        $password = isset($config['password']) ? $config['password'] : '';

        // Call the PDO constructor.
        parent::__construct($dsn, $username, $password, $options);

        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Get the name of the driver
     * @return string
     */
    abstract public function getDriverName();

    /**
     * Get driver code, used in config as driver string.
     * @return string
     */
    abstract public function getDriverCode();

    /**
     * Get the current fetching Method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get configuration for instance
     * @return array
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * Get native connection. Could be \PDO
     * @return \PDO
     */
    public function getConnection()
    {
        return $this;
    }

    /**
     * Basic execute statement. Only for queries with no binding parameters
     * This method is not SQL Injection safe! Please remember to don't use this with dynamic content!
     * This will only return an array or boolean. Depends on your operation and if fetch is on.
     *
     * @param $sql
     * @param $fetch
     * @return mixed
     */
    public function raw($sql, $fetch = false)
    {
        $method = $this->method;

        if ($this->method === \PDO::FETCH_CLASS) {
            // We can't fetch class here to stay conform the interface, make it OBJ for this simple query.
            $method = \PDO::FETCH_OBJ;
        }

        $this->queryCount++;

        if (!$fetch) {
            return $this->exec($sql);
        }

        $statement = $this->query($sql, $method);

        return $statement->fetchAll();
    }

    public function rawQuery($sql)
    {
        // We can't fetch class here to stay conform the interface, make it OBJ for this simple query.
        $method = ($this->method !== \PDO::FETCH_CLASS) ? $this->method : \PDO::FETCH_OBJ;

        $this->queryCount++;

        // We don't want to map in memory an entire Billion Records Table, so we return right on a Statement.
        return $this->query($sql, $method);
    }

    /**
     * Execute Select Query, bind values into the $sql query. And give optional method and class for fetch result
     * The result MUST be an array!
     *
     * @param string $sql
     * @param array $bindParams
     * @param bool $fetchAll Ask the method to fetch all the records or not.
     * @param null $method Customized method for fetching, null for engine default or config default.
     * @param null $class Class for fetching into classes.
     * @return array|null
     *
     * @throws \Exception
     */
    public function select($sql, $bindParams = array(), $fetchAll = false, $method = null, $class = null)
    {
        // Append select if it isn't appended.
        if (strtolower(substr($sql, 0, 7)) !== 'select ') {
            $sql = "SELECT " . $sql;
        }

        // What method? Use default if no method is given my the call.
        if ($method === null) {
            $method = $this->method;
        }

        // Prepare and get statement from PDO.
        $stmt = $this->prepare($sql);

        // Bind the key and values (only if given).
        foreach ($bindParams as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue("$key", $value, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue("$key", $value);
            }
        }

        // Execute, we should capture the status of the result.
        $status = $stmt->execute();

        $this->queryCount++;

        // If failed, return now, and don't continue with fetching.
        if (!$status) {
            return false;
        }

        if($fetchAll) {
            // Continue with fetching all records.
            if ($method === \PDO::FETCH_CLASS) {
                if (!$class) {
                    throw new \Exception("No class is given but you are using the PDO::FETCH_CLASS method!");
                }

                // Fetch in class
                $result = $stmt->fetchAll($method, $class);
            }
            else {
                $result = $stmt->fetchAll($method);
            }

            if (is_array($result) && count($result) > 0) {
                return $result;
            }

            return false;
        }

        // Continue with fetching one record.
        if ($method === \PDO::FETCH_CLASS) {
            if (!$class) {
                throw new \Exception("No class is given but you are using the PDO::FETCH_CLASS method!");
            }

            // Fetch in class
            return $stmt->fetch($method, $class);
        }
        else {
            return $stmt->fetch($method);
        }
    }

    /**
     * Convenience method for fetching one record.
     *
     * @param string $sql
     * @param array $bindParams
     * @param null $method Customized method for fetching, null for engine default or config default.
     * @param null $class Class for fetching into classes.
     * @return object|array|null|false
     * @throws \Exception
     */
    public function selectOne($sql, $bindParams = array(), $method = null, $class = null)
    {
        return $this->select($sql, $bindParams, false, $method, $class);
    }

    /**
     * Convenience method for fetching all records.
     *
     * @param string $sql
     * @param array $bindParams
     * @param null $method Customized method for fetching, null for engine default or config default.
     * @param null $class Class for fetching into classes.
     * @return array|null|false
     * @throws \Exception
     */
    public function selectAll($sql, $bindParams = array(), $method = null, $class = null)
    {
        return $this->select($sql, $bindParams, true, $method, $class);
    }

    /**
     * Execute insert query, will automatically build query for you.
     * You can also give an array as $data, this will try to insert each entry in the array.
     * Not all engine's support this! Check the manual!
     *
     * @param string $table Table to execute the insert.
     * @param array $data Represents one record, could also have multidimensional arrays inside to insert
     *                    multiple rows in one call. The engine must support this! Check manual!
     * @param bool $transaction Use PDO Transaction. If one insert will fail we will rollback immediately. Default false.
     * @return int|bool|array Could be false on error, or one single id inserted, or an array of inserted id's.
     *
     * @throws \Exception
     */
    public function insert($table, $data, $transaction = false)
    {
        $insertId = 0;

        // Check for valid data.
        if (!is_array($data)) {
            throw new \Exception("Data to insert must be an array of column -> value.");
        }

        // Transaction?
        $status = false;

        if ($transaction) {
            $status = $this->beginTransaction();
        }

        // Holding status
        $failure = false;

        $ids = array();

        // Prepare the parameters.
        ksort($data);

        $fieldNames = implode(',', array_keys($data));
        $fieldValues = ':'.implode(', :', array_keys($data));

        $stmt = $this->prepare("INSERT INTO $table ($fieldNames) VALUES ($fieldValues)");

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        // Execute
        $this->queryCount++;

        if (! $stmt->execute()) {
            $failure = true;
        }
        else {
            // If no error, capture the last inserted id
            $insertId = $this->lastInsertId();
        }

        // Commit when in transaction
        if (! $failure && $transaction && $status) {
            $failure = ! $this->commit();
        }

        // Check for failures
        if ($failure) {
            // Ok, rollback when using transactions.
            if ($transaction) {
                $this->rollBack();
            }

            // False on error.
            return false;
        }

        return $insertId;
    }

    /**
     * Execute insert query, will automatically build query for you.
     * You can also give an array as $data, this will try to insert each entry in the array.
     * Not all engine's support this! Check the manual!
     *
     * @param string $table Table to execute the insert.
     * @param array $data Represents one record, could also have multidimensional arrays inside to insert
     *                    multiple rows in one call. The engine must support this! Check manual!
     * @param bool $transaction Use PDO Transaction. If one insert will fail we will rollback immediately. Default false.
     * @return int|bool|array Could be false on error, or one single id inserted, or an array of inserted id's.
     *
     * @throws \Exception
     */
    abstract public function insertBatch($table, $data, $transaction = false);

    /**
     * Execute update query, will automatically build query for you.
     *
     * @param string $table Table to execute the statement.
     * @param array $data The updated array, will map into an update statement.
     * @param array $where Use key->value like column->value for where mapping.
     * @return int|bool
     *
     * @throws \Exception
     */
    public function update($table, $data, $where)
    {
        // Sort on key
        ksort($data);

        // Column :bind for auto binding.
        $fieldDetails = null;

        foreach ($data as $key => $value) {
            $fieldDetails .= "$key = :field_$key,";
        }

        $fieldDetails = rtrim($fieldDetails, ',');

        // Where :bind for auto binding
        $whereDetails = null;
        $idx = 0;

        foreach ($where as $key => $value) {
            if ($idx == 0) {
                $whereDetails .= "$key = :where_$key";
            } else {
                $whereDetails .= " AND $key = :where_$key";
            }
            $idx++;
        }

        $whereDetails = ltrim($whereDetails, ' AND ');

        // Prepare statement.
        $stmt = $this->prepare("UPDATE $table SET $fieldDetails WHERE $whereDetails");

        // Bind fields
        foreach ($data as $key => $value) {
            $stmt->bindValue(":field_$key", $value);
        }

        // Bind values
        foreach ($where as $key => $value) {
            $stmt->bindValue(":where_$key", $value);
        }

        // Execute
        $this->queryCount++;

        if (!$stmt->execute()) {
            return false;
        }

        // Row count, affected rows
        return $stmt->rowCount();
    }

    /**
     * Execute Delete statement, this will automatically build the query for you.
     *
     * @param string $table Table to execute the statement.
     * @param array $where Use key->value like column->value for where mapping.
     * @return bool|int Row Count, number of deleted rows, or false on failure.
     *
     * @throws \Exception
     */
    public function delete($table, $where)
    {
        // Sort in where keys.
        ksort($where);

        // Bind the where details.
        $whereDetails = null;
        $idx = 0;

        foreach ($where as $key => $value) {
            if ($idx == 0) {
                $whereDetails .= "$key = :$key";
            } else {
                $whereDetails .= " AND $key = :$key";
            }
            $idx++;
        }

        $whereDetails = ltrim($whereDetails, ' AND ');

        // Prepare statement
        $stmt = $this->prepare("DELETE FROM $table WHERE $whereDetails");

        // Bind
        foreach ($where as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        // Execute and return if failure.
        $this->queryCount++;

        if (!$stmt->execute()) {
            return false;
        }

        // Return rowcount when succeeded.
        return $stmt->rowCount();
    }

    /**
     * Prepare the query and return a prepared statement.
     * Optional bind is available.
     *
     * @param string $sql Query
     * @param array $bind optional binding values
     * @param int|null $method custom method
     * @param string|null $class class fetch, the class, full class with namespace.
     * @return \PDOStatement|mixed
     *
     * @throws \Exception
     */
    public function rawPrepare($sql, $bind = array(), $method = null, $class = null)
    {

        // Prepare and get statement from PDO.
        $stmt = $this->prepare($sql);

        // Bind the key and values (only if given).
        foreach ($bind as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue("$key", $value, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue("$key", $value);
            }
        }

        $this->queryCount++;

        return $stmt;
    }

    /**
     * Truncate table
     * @param  string $table table name
     * @return int number of rows affected
     */
    abstract public function truncate($table);

    /**
     * Get total executed queries.
     *
     * @return int
     */
    public function getTotalQueries()
    {
        return $this->queryCount;
    }
}
