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

class MySQL extends \PDO implements Engine
{
    /** @var int PDO Fetch method. */
    private $method = \PDO::FETCH_OBJ;
    /** @var array Config from the user's app config. */
    private $config;
    /** @var int Counting how much queries have been executed in total. */
    private $queryCount;

    /**
     * MySQLEngine constructor.
     * Please use the Factory to maintain instances of the drivers.
     *
     * @param $config array
     *
     * @throws \PDOException
     */
    public function __construct($config) {
        // Check for valid Config.
        if (!is_array($config)) {
            throw new \UnexpectedValueException('Parameter should be an Array');
        }

        // Will set the default method when provided in the config.
        if (isset($config['fetch_method'])) {
            $this->method = $config['fetch_method'];
        }

        // Default port if no port is provided.
        if (!isset($config['port'])) {
            $config['port'] = 3306;
        }

        // Some Database Servers go crazy when a charset parameter is added, then we should make that parameter optional.
        if (!isset($config['charset'])) {
            $charsetStr = "";
        }
        else {
            $charsetStr = ($config['charset'] == 'auto') ? "" : ";charset=" . $config['charset'];
        }

        // Reset query counter
        $this->queryCount = 0;

        // Set config in class variable.
        $this->config = $config;

        $dsn = "mysql:host=" . $config['host'] . ";port=" . $config['port'] . ";dbname=" . $config['database'] . $charsetStr;

        parent::__construct($dsn, $config['username'], $config['password'], array(\PDO::MYSQL_ATTR_COMPRESS => true));

        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Get the name of the driver
     * @return string
     */
    public function getDriverName()
    {
        return "MySQL Driver";
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
     * Get driver code, used in config as driver string.
     * @return string
     */
    public function getDriverCode()
    {
        return Manager::DRIVER_MYSQL;
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
     * @return array|null
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
     * @param bool $multipleInserts Specify to execute multiple inserts.
     * @return int|bool|array Could be false on error, or one single id inserted, or an array of inserted id's.
     *
     * @throws \Exception
     */
    public function insert($table, $data, $transaction = false, $multipleInserts = false)
    {
        // Check for valid data.
        if (!is_array($data)) {
            throw new \Exception("Data to insert must be an array of column -> value. MySQL Driver supports multidimensional multiple inserts.");
        }

        if (! $multipleInserts) {
            // Currently not using the multi insert, make data to use same code.
            $data = array($data);
        }

        // Transaction?
        $transactionStatus = false;

        if ($transaction) {
            $transactionStatus = $this->beginTransaction();
        }

        // Holding status
        $failure = false;
        $ids = array();

        // Loop every record to insert
        foreach($data as $record) {
            ksort($record);

            $fieldNames = implode(',', array_keys($record));
            $fieldValues = ':'.implode(', :', array_keys($record));

            $stmt = $this->prepare("INSERT INTO $table ($fieldNames) VALUES ($fieldValues)");

            foreach ($record as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            // Execute
            $this->queryCount++;

            if (!$stmt->execute()) {
                $failure = true;

                // We need to exit foreach, to inform about the error, or rollback.
                break 1;
            }

            // If no error, capture the last inserted id
            $ids[] = $this->lastInsertId();
        }

        // Commit when in transaction
        if ($transaction && $transactionStatus) {
            $failure = !$this->commit();
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

        if (! $multipleInserts) {
            return (count($ids) == 1) ? array_shift($ids) : 0;
        }

        return $ids;
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
    public function insertAll($table, $data, $transaction = false)
    {
        // Check for valid data.
        if (!is_array($data)) {
            throw new \Exception("Data to insert must be an array of column -> value. MySQL Driver supports multidimensional multiple inserts.");
        }

        return $this->insert($table, $data, $transaction, is_array($data[0]));
    }

    /**
     * Execute update query, will automatically build query for you.
     *
     * @param string $table Table to execute the statement.
     * @param array $data The updated array, will map into an update statement.
     * @param array $where Use key->value like column->value for where mapping.
     * @param int $limit Limit the update statement, not supported by every engine!
     * @return int|bool
     *
     * @throws \Exception
     */
    public function update($table, $data, $where, $limit = 1)
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

        // Limit
        $optionalLimit = "";

        if (is_numeric($limit)) {
            $optionalLimit = " LIMIT " . $limit;
        }

        // Prepare statement.
        $stmt = $this->prepare("UPDATE $table SET $fieldDetails WHERE $whereDetails $optionalLimit");

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
     * @param int $limit Limit the update statement, not supported by every engine!
     * @return bool|int Row Count, number of deleted rows, or false on failure.
     *
     * @throws \Exception
     */
    public function delete($table, $where, $limit = 1)
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

        // If limit is a number use a limit on the query
        $optionalLimit = "";

        if (is_numeric($limit)) {
            $optionalLimit = "LIMIT $limit";
        }

        // Prepare statement
        $stmt = $this->prepare("DELETE FROM $table WHERE $whereDetails $optionalLimit");

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
     * Truncate table
     * @param  string $table table name
     * @return int number of rows affected
     */
    public function truncate($table)
    {
        $this->queryCount++;

        return $this->exec("TRUNCATE TABLE $table");
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
     * Get total executed queries.
     *
     * @return int
     */
    public function getTotalQueries()
    {
        return $this->queryCount;
    }
}
