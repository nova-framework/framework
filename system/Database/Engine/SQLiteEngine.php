<?php


namespace Nova\Database\Engine;


use Nova\Database\EngineFactory;

class SQLiteEngine extends \PDO implements Engine, GeneralEngine
{
    /** @var int PDO Fetch method. */
    private $method = \PDO::FETCH_OBJ;
    /** @var array Config from the user's app config. */
    private $config;


    /**
     * SQLiteEngine constructor.
     * Please use the Factory to maintain instances of the drivers.
     *
     * @param $config array
     * @throws \PDOException
     */
    public function __construct($config) {
        // Will set the default method when provided in the config.
        if (isset($config['fetch_method'])) {
            $this->method = $config['fetch_method'];
        }

        // Set config in class variable.
        $this->config = $config;

        $dsn = "sqlite:" . BASEPATH . 'storage' . DS . 'persistent' . DS . $config['file'];

        parent::__construct($dsn);
        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Get the name of the driver
     * @return string
     */
    public function getDriverName()
    {
        return "SQLite Driver";
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
     * @return mixed|\PDO
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
        return EngineFactory::DRIVER_SQLITE;
    }

    /**
     * Basic execute statement. Only for small queries with no binding parameters
     *
     * @param $sql
     * @return mixed
     */
    public function executeSimpleQuery($sql)
    {
        $method = $this->method;
        if ($this->method === \PDO::FETCH_CLASS) {
            // We can't fetch class here to stay conform the interface, make it OBJ for this simple query.
            $method = \PDO::FETCH_OBJ;
        }

        $statement = $this->query($sql, $method);

        return $statement->fetchAll();
    }

    /**
     * Execute Query, bind values into the $sql query. And give optional method and class for fetch result
     * The result MUST be an array!
     *
     * @param string $sql
     * @param array $bind
     * @param null $method Customized method for fetching, null for engine default or config default.
     * @param null $class Class for fetching into classes.
     * @return array|null
     *
     * @throws \Exception
     */
    function executeQuery($sql, $bind = array(), $method = null, $class = null)
    {
        // What method? Use default if no method is given my the call.
        if ($method === null) {
            $method = $this->method;
        }

        // Prepare statement
        $stmt = $this->prepare($sql);

        // Bind values
        foreach ($bind as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue("$key", $value, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue("$key", $value);
            }
        }

        // Execute, hold status
        $status = $stmt->execute();

        // If failed, return now, and don't continue with fetching.
        if (!$status) {
            return false;
        }

        // Continue with fetching all records.
        if ($method === \PDO::FETCH_CLASS) {
            if (!$class) {
                throw new \Exception("No class is given but you are using the PDO::FETCH_CLASS method!");
            }

            // Fetch in class
            $result = $stmt->fetchAll($method, $class);
        } else {
            // We will fetch here too ;)
            $result = $stmt->fetchAll($method);
        }

        if (is_array($result) && count($result) > 0) {
            return $result;
        }
        return false;
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
     * @return int|bool
     *
     * @throws \Exception
     */
    function executeInsert($table, $data, $transaction = false)
    {
        // Check for valid data.
        if (!is_array($data)) {
            throw new \Exception("Data to insert must be an array of column -> value. MySQL Driver supports multidimensional multiple inserts.");
        }

        // Check for multidimensional, multiple inserts
        if (!is_array($data[0])) {
            // Currently not multi insert, make it to use same code.
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

        if (count($ids) === 1) {
            return $ids[0];
        }
        return $ids;
    }

















    /**
     * Select from the database
     *
     * @param  string      $sql       sql query, leave out the SELECT word
     * @param  array       $array     named params
     * @param  int         $fetchMode Fetch mode (use \PDO::FETCH_*)
     * @param  string|null $class     class name for using with \PDO::FETCH_CLASS
     * @return array                  returns an array of records
     */
    public function select($sql, $array = array(), $fetchMode = \PDO::FETCH_OBJ, $class = null)
    {
        if (strtolower(substr($sql, 0, 7)) !== 'select ') {
            $sql = "SELECT " . $sql;
        }
        $stmt = $this->prepare($sql);
        foreach ($array as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue("$key", $value, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue("$key", $value);
            }
        }

        $stmt->execute();

        $fetched = array();
        if ($fetchMode === \PDO::FETCH_CLASS) {
            $fetched = $stmt->fetchAll($fetchMode, $class);
        } else {
            $fetched = $stmt->fetchAll($fetchMode);
        }


        if (is_array($fetched) && count($fetched) > 0) {
            return $fetched;
        }
        return false;
    }

    /**
     * Insert data in table
     * @param  string $table table name
     * @param  array $data  array of columns and values
     * @return int|false inserted id or false on failure
     */
    public function insert($table, $data)
    {
        ksort($data);

        $fieldNames = implode(',', array_keys($data));
        $fieldValues = ':'.implode(', :', array_keys($data));

        $stmt = $this->prepare("INSERT INTO $table ($fieldNames) VALUES ($fieldValues)");

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        if (!$stmt->execute()) {
            return false;
        }
        return $this->lastInsertId();
    }

    /**
     * Update data in table
     * @param  string $table table name
     * @param  array $data  array of columns and values
     * @param  array $where array of columns and values
     * @return int|false Row count or false on failure
     */
    public function update($table, $data, $where)
    {
        ksort($data);

        $fieldDetails = null;
        foreach ($data as $key => $value) {
            $fieldDetails .= "$key = :field_$key,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');

        $whereDetails = null;
        $i = 0;
        foreach ($where as $key => $value) {
            if ($i == 0) {
                $whereDetails .= "$key = :where_$key";
            } else {
                $whereDetails .= " AND $key = :where_$key";
            }
            $i++;
        }
        $whereDetails = ltrim($whereDetails, ' AND ');

        $stmt = $this->prepare("UPDATE $table SET $fieldDetails WHERE $whereDetails");

        foreach ($data as $key => $value) {
            $stmt->bindValue(":field_$key", $value);
        }

        foreach ($where as $key => $value) {
            $stmt->bindValue(":where_$key", $value);
        }

        if (!$stmt->execute()) {
            return false;
        }
        return $stmt->rowCount();
    }

    /**
     * Delete method
     *
     * @param  string $table table name
     * @param  array $where array of columns and values
     * @return int|false Row count or false on failure
     */
    public function delete($table, $where)
    {
        ksort($where);

        $whereDetails = null;
        $i = 0;
        foreach ($where as $key => $value) {
            if ($i == 0) {
                $whereDetails .= "$key = :$key";
            } else {
                $whereDetails .= " AND $key = :$key";
            }
            $i++;
        }
        $whereDetails = ltrim($whereDetails, ' AND ');

        $stmt = $this->prepare("DELETE FROM $table WHERE $whereDetails");

        foreach ($where as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        if (!$stmt->execute()) {
            return false;
        }
        return $stmt->rowCount();
    }

}
