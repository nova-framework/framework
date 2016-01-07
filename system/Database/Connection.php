<?php
/**
 * MySQL Engine.
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 27th, 2015
 */

namespace Nova\Database;

use Nova\Database\Manager;
use Nova\Database\QueryBuilder;

use PDO;


abstract class Connection extends PDO
{
    /** @var string Return type. */
    protected $returnType = 'array';

    /** @var array Config from the user's app config. */
    protected $config;

    /** @var int Counting how much queries have been executed in total. */
    protected $queryCount = 0;


    /**
     * MySQLEngine constructor.
     * Please use the Factory to maintain instances of the drivers.
     *
     * @param $dsn
     * @param $config array
     *
     * @param array $options
     * @throws \Exception
     */
    public function __construct($dsn, $config = array(), $options = array()) {
        // Check for valid Config.
        if (! is_array($config) || ! is_array($options)) {
            throw new \UnexpectedValueException('Config and Options parameters should be Arrays');
        }

        // Will set the default method when provided in the config.
        if (isset($config['return_type'])) {
            $this->returnType = $config['return_type'];
        }

        if($this->returnType == 'array') {
            $fetchMethod = PDO::FETCH_ASSOC;
        }
        else if($this->returnType == 'object') {
            $fetchMethod = PDO::FETCH_OBJ;
        }
        else {
            $classPath = str_replace('\\', '/', ltrim($this->returnType, '\\'));

            if(! preg_match('#^App(?:/Modules/.+)?/Models/Entities/(.*)$#i', $classPath)) {
                throw new \Exception("No valid Entity Name is given: " .$this->returnType);
            }

            if(! class_exists($this->returnType)) {
                throw new \Exception("No valid Entity Class is given: " .$this->returnType);
            }

            $fetchMethod = PDO::FETCH_CLASS;
        }

        // Store the config in class variable.
        $this->config = $config;

        //
        $username = isset($config['user']) ? $config['user'] : '';
        $password = isset($config['password']) ? $config['password'] : '';

        // Call the PDO constructor.
        parent::__construct($dsn, $username, $password, $options);

        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $fetchMethod);
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
     * Set/Get the fetching return type.
     */
    public function returnType($type = null)
    {
        if($type === null) {
            return $this->returnType;
        }

        $this->returnType = $type;
    }

    /**
     * Get configuration for instance
     * @return array
     */
    public function getOptions()
    {
        return $this->config;
    }

    /**
     * Get native connection. Could be PDO
     * @return PDO
     */
    public function getLink()
    {
        return $this;
    }

    public function getQueryBuilder()
    {
        return new QueryBuilder($this);
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
        // We can't fetch class here to stay conform the interface, make it OBJ for this simple query.
        $method = ($this->returnType == 'array') ? PDO::FETCH_ASSOC : PDO::FETCH_OBJ;

        $this->queryCount++;

        if (!$fetch) {
            return $this->exec($sql);
        }

        $statement = $this->query($sql, $method);

        return $statement->fetchAll();
    }

    public function rawQuery($sql, $returnType = null)
    {
        // What return type? Use default if no return type is given in the call.
        $returnType = $returnType ? $returnType : $this->returnType;

        // We can't fetch class here to stay conform the interface, make it OBJ for this simple query.
        $method = ($returnType == 'array') ? PDO::FETCH_ASSOC : PDO::FETCH_OBJ;

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
     * @param null $returnType Customized method for fetching, null for engine default or config default.
     * @return array|null
     *
     * @throws \Exception
     */
    public function select($sql, $bindParams = array(), $fetchAll = false, $returnType = null)
    {
        // Append select if it isn't appended.
        if (strtolower(substr($sql, 0, 7)) !== 'select ') {
            $sql = "SELECT " . $sql;
        }

        // What return type? Use default if no return type is given in the call.
        $returnType = $returnType ? $returnType : $this->returnType;

        // Prepare the parameters.
        $className = null;

        if($returnType == 'array') {
            $fetchMethod = PDO::FETCH_ASSOC;
        }
        else if($returnType == 'object') {
            $fetchMethod = PDO::FETCH_OBJ;
        }
        else {
            $classPath = str_replace('\\', '/', ltrim($returnType, '\\'));

            if(! preg_match('#^App(?:/Modules/.+)?/Models/Entities/(.*)$#i', $classPath)) {
                throw new \Exception("No valid Entity Name is given: " .$returnType);
            }

            if(! class_exists($returnType)) {
                throw new \Exception("No valid Entity Class is given: " .$returnType);
            }

            $className = $returnType;

            $fetchMethod = PDO::FETCH_CLASS;
        }

        // Prepare and get statement from PDO.
        $stmt = $this->prepare($sql);

        // Bind the key and values (only if given).
        foreach ($bindParams as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue(":$key", $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":$key", $value);
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
            if ($fetchMethod === PDO::FETCH_CLASS) {
                // Fetch in class
                $result = $stmt->fetchAll($fetchMethod, $className);
            }
            else {
                $result = $stmt->fetchAll($fetchMethod);
            }

            if (is_array($result) && count($result) > 0) {
                return $result;
            }

            return false;
        }

        // Continue with fetching one record.
        if ($fetchMethod === PDO::FETCH_CLASS) {
            // Fetch in class
            return $stmt->fetch($fetchMethod, $className);
        }
        else {
            return $stmt->fetch($fetchMethod);
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
    public function selectOne($sql, $bindParams = array(), $returnType = null)
    {
        return $this->select($sql, $bindParams, false, $returnType);
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
    public function selectAll($sql, $bindParams = array(), $returnType = null)
    {
        return $this->select($sql, $bindParams, true, $returnType);
    }

    /**
     * Execute insert query, will automatically build query for you.
     *
     * @param string $table Table to execute the insert.
     * @param array $data Represents one record, could also have multidimensional arrays inside to insert
     *                    multiple rows in one call. The engine must support this! Check manual!
     * @param bool $transaction Use PDO Transaction. If one insert will fail we will rollback immediately. Default false.
     * @param string $mode Represents the insertion Mode, must be 'insert' or 'replace'
     * @return int|bool|array Could be false on error, or one single id inserted, or an array of inserted id's.
     *
     * @throws \Exception
     */
    public function insert($table, $data, $transaction = false, $mode = 'insert')
    {
        $insertId = 0;

        if(($mode != 'insert') && ($mode != 'replace')) {
            throw new \Exception("Insert Mode must be 'insert' or 'replace'");
        }
        else {
            $mode = strtoupper($mode);
        }

        // Check for valid data.
        if (! is_array($data)) {
            throw new \Exception("Data to insert must be an array of column -> value.");
        }

        // Transaction?
        $status = false;

        if ($transaction) {
            $status = $this->beginTransaction();
        }

        // Holding status
        $failure = false;

        // Prepare the parameters.
        ksort($data);

        $fieldNames = implode(',', array_keys($data));
        $fieldValues = ':'.implode(', :', array_keys($data));

        $stmt = $this->prepare("$mode INTO $table ($fieldNames) VALUES ($fieldValues)");

        foreach ($data as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue(":$key", $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":$key", $value);
            }
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
    public function insertBatch($table, $data, $transaction = false)
    {
        // Check for valid data.
        if (! is_array($data)) {
            throw new \Exception("Data to insert must be an array of records (array of array with column -> value).");
        }

        foreach($data as $record) {
            if (is_array($record)) {
                continue;
            }

            throw new \Exception("Data to insert must be an array of records (array of array with column -> value).");
        }

        // Transaction?
        $status = false;

        if ($transaction) {
            $status = $this->beginTransaction();
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
                if (is_int($value)) {
                    $stmt->bindValue(":$key", $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue(":$key", $value);
                }
            }

            // Execute
            $this->queryCount++;

            if (! $stmt->execute()) {
                $failure = true;

                // We need to exit foreach, to inform about the error, or rollback.
                break 1;
            }

            // If no error, capture the last inserted id
            $ids[] = $this->lastInsertId();
        }

        // Commit when in transaction
        if (! $failure && $transaction && $status) {
            $failure = ! $this->commit();
        }

        // Check for failures
        if ($failure) {
            // Ok, rollback when using transactions.
            if ($transaction && $status) {
                $this->rollBack();
            }

            // False on error.
            return false;
        }

        return $ids;
    }

    /**
     * Performs the SQL standard for a combined DELETE + INSERT, using PRIMARY and UNIQUE keys to determine which row to replace.
     *
     * @param string $table Table to execute the replace.
     * @param array $data Represents the Record data
     * @param bool $transaction Use PDO Transaction. If one replace will fail we will rollback immediately. Default false.
     * @return int|bool|array Could be false on error, or one single ID replaced.
     *
     * @throws \Exception
     */
    public function replace($table, $data, $transaction = false)
    {
        return $this->insert($table, $data, $transaction, 'replace');
    }

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
        $bindParams = array();

        // Sort on key
        ksort($data);

        // Column :bind for auto binding.
        $fieldDetails = '';

        $idx = 0;

        foreach ($data as $key => $value) {
            if($idx > 0) {
                $fieldDetails .= ', ';
            }

            $fieldDetails .= "$key = :field_$key";

            $idx++;
        }


        // Sort in where keys.
        ksort($where);

        // Where :bind for auto binding
        $whereDetails = '';

        if(is_array($where)) {
            $idx = 0;

            foreach ($where as $key => $value) {
                if($idx > 0) {
                    $whereDetails .= ' AND ';
                }

                $idx++;

                if(empty($value)) {
                    // A string based condition; simplify its white spaces and use it directly.
                    $whereDetails .= preg_replace('/\s+/', ' ', trim($key));

                    continue;
                }

                if(strpos($key, ' ') !== false) {
                    $key = preg_replace('/\s+/', ' ', trim($key));

                    $segments = explode(' ', $key);

                    $key      = $segments[0];
                    $operator = $segments[1];
                }
                else {
                    $operator = '=';
                }

                $whereDetails .= "$key $operator :where_$key";

                $bindParams[$key] = $value;
            }
        }
        else if(is_string($where)) {
            $whereDetails = $where;
        }

        // Prepare statement.
        $stmt = $this->prepare("UPDATE $table SET $fieldDetails WHERE $whereDetails");

        // Bind fields
        foreach ($data as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue(":field_$key", $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":field_$key", $value);
            }
        }

        // Bind values
        foreach ($bindParams as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue(":where_$key", $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":where_$key", $value);
            }
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
     * Updates multiple records in the database at once.
     *
     * $data = array(
     *     array(
     *         'title'  => 'My title',
     *         'body'   => 'body 1'
     *     ),
     *     array(
     *         'title'  => 'Another Title',
     *         'body'   => 'body 2'
     *     )
     * );
     *
     * The $whereKey should be the name of the column to match the record on.
     * If $whereKey == 'title', then each record would be matched on that
     * 'title' value of the array. This does mean that the array key needs
     * to be provided with each row's data.
     *
     * The $whereKey could also be an array with column names, this is usefull
     * when having multiple primary keys in your table.
     *
     * @param  string $table The Table name.
     * @param  array $data An associate array of row data to update.
     * @param  string|array $whereKey The column name to match on.
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function updateBatch($table, $data, $whereKey)
    {
        // Check for valid data
        if (! is_array($data)) {
            throw new \Exception("Data to update must be an array of records (array of array with column -> value).");
        }

        foreach($data as $record) {
            if (is_array($record)) {
                continue;
            }

            throw new \Exception("Data to update must be an array of records (array of array with column -> value).");
        }

        // Always make an array for the where keys.
        $whereKeys = (is_array($whereKey) ? $whereKey : array($whereKey));

        // Make the where statement
        $whereDetails = '';
        $idx = 0;

        foreach ($whereKeys as $key => $value) {
            if($idx > 0) {
                $whereDetails .= ' AND ';
            }
            $whereDetails .= "$value = :where_$key";
            $idx++;
        }


        // Perform the batch update per record
        foreach($data as $record) {
            // Sort on key
            ksort($record);

            // Column (bind for auto binding).
            $fieldDetails = '';
            $idx = 0;

            foreach ($record as $key => $value) {
                if($idx > 0) {
                    $fieldDetails .= ', ';
                }

                $fieldDetails .= "$key = :field_$key";

                $idx++;
            }

            // Prepare statement.
            $stmt = $this->prepare("UPDATE $table SET $fieldDetails WHERE $whereDetails");

            // Bind fields
            foreach ($record as $key => $value) {
                if (is_int($value)) {
                    $stmt->bindValue(":field_$key", $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue(":field_$key", $value);
                }
            }

            // Bind where
            foreach ($whereKeys as $key => $column) {
                $value = $record[$column];

                if (is_int($value)) {
                    $stmt->bindValue(":where_$key", $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue(":where_$key", $value);
                }
            }

            // Execute
            $this->queryCount++;

            // Directly stop at error.
            if (!$stmt->execute()) {
                return false;
            }
        }

        return true;
    }


    /**
     * Execute Delete statement, this will automatically build the query for you.
     *
     * @param string $table Table to execute the statement.
     * @param array|string $where Use a string or key->value like column->value for where mapping.
     * @return bool|int Row Count, number of deleted rows, or false on failure.
     *
     * @throws \Exception
     */
    public function delete($table, $where)
    {
        $bindParams = array();

        // Prepare the WHERE details.
        $whereDetails = '';

        if(is_array($where)) {
            ksort($where);

            $idx = 0;

            foreach ($where as $key => $value) {
                if($idx > 0) {
                    $whereDetails .= ' AND ';
                }

                if(strpos($key, ' ') !== false) {
                    $key = preg_replace('/\s+/', ' ', trim($key));

                    $segments = explode(' ', $key);

                    $key      = $segments[0];
                    $operator = $segments[1];
                }
                else {
                    $operator = '=';
                }

                $whereDetails .= "$key $operator :$key";

                //
                $bindParams[$key] = $value;

                $idx++;
            }
        }
        else if(is_string($where)) {
            $whereDetails = $where;
        }

        // Prepare statement
        $stmt = $this->prepare("DELETE FROM $table WHERE $whereDetails");

        // Bind parameters.
        foreach ($bindParams as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue(":$key", $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":$key", $value);
            }
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
     * @param array $bindParams optional binding values
     * @return PDOStatement|mixed
     *
     * @throws \Exception
     */
    public function rawPrepare($sql, $bindParams = array())
    {
        // Prepare and get statement from PDO.
        $stmt = $this->prepare($sql);

        // Bind the key and values (only if given).
        foreach ($bindParams as $key => $value) {
            if (substr($key, 0, 1) !== ':') {
                $key = ':' . $key;
            }

            if (is_int($value)) {
                $stmt->bindValue("$key", $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue("$key", $value);
            }
        }

        $this->queryCount++;

        return $stmt;
    }


    /**
     * Quote escape a string for using in a query.
     *
     * WARNING: You can better prepare a query and bind values in that way.
     * This method could not be always safe.
     *
     * @param $string String to be escaped
     * @param int $parameter_type Optional parameter type.
     *
     * @return string|false Quoted string or false on failure.
     */
    public function escape($string, $parameter_type = PDO::PARAM_STR)
    {
        return parent::quote($string, $parameter_type);
    }

    /**
     * Truncate table
     * @param  string $table table name
     * @return int number of rows affected
     */
    abstract public function truncate($table);

    /**
     * Get the field names for the specified Database Table.
     *
     * @param  string $table table name
     * @return array  Returns the Database Table fields
     */
    abstract public function listFields($table);

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
