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

use \FluentStructure;
use \PDO;


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
            throw new \UnexpectedValueException(__d('system', 'Config and Options parameters should be Arrays'));
        }

        // Will set the default method when provided in the config.
        if (isset($config['return_type'])) {
            $this->returnType = $config['return_type'];
        }

        // Prepare the FetchMethod and check the returnType
        $fetchMethod = self::getFetchMethod($this->returnType);

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

    public function getQueryBuilder(FluentStructure $structure = null)
    {
        return new QueryBuilder($this, $structure);
    }

    public static function getFetchMethod($returnType, &$fetchClass = null) {
        // Prepare the parameters.
        $className = null;

        if($returnType == 'array') {
            $fetchMethod = PDO::FETCH_ASSOC;
        }
        else if($returnType == 'object') {
            $fetchMethod = PDO::FETCH_OBJ;
        }
        else {
            $fetchMethod = PDO::FETCH_CLASS;

            // Check and setup the className.
            $classPath = str_replace('\\', '/', ltrim($returnType, '\\'));

            if(! preg_match('#^App(?:/Modules/.+)?/Models/Entities/(.*)$#i', $classPath)) {
                throw new \Exception(__d('system', 'No valid Entity Name is given: {0}', $returnType));
            }

            if(! class_exists($fetchType)) {
                throw new \Exception(__d('system', 'No valid Entity Class is given: {0}', $returnType));
            }

            $fetchClass = $returnType;
        }

        return $fetchMethod;
    }

    public static function getParamTypes(array $params)
    {
        $result = array();

        foreach ($params as $key => $value) {
            if (is_integer($value)) {
                $result[$key] = PDO::PARAM_INT;
            }
            else if (is_bool($value)) {
                $result[$key] = PDO::PARAM_BOOL;
            }
            else if(is_null($value)) {
                $result[$key] = PDO::PARAM_NULL;
            }
            else {
                $result[$key] = PDO::PARAM_STR;
            }
        }

        return $result;
    }

    public function bindParams($statement, array $params, array $paramTypes = array(), $prefix = ':')
    {
        if(empty($params)) {
            return;
        }

        // Bind the key and values (only if given).
        foreach ($params as $key => $value) {
            if(isset($paramTypes[$key])) {
                $statement->bindValue($prefix .$key, $value, $paramTypes[$key]);

                continue;
            }

            // No parameter Type found, we try our best of to guess it.
            if (is_integer($value)) {
                $statement->bindValue($prefix .$key, $value, PDO::PARAM_INT);
            }
            else if (is_bool($value)) {
                $statement->bindValue($prefix .$key, $value, PDO::PARAM_BOOL);
            }
            else if(is_null($value)) {
                $statement->bindValue($prefix .$key, $value, PDO::PARAM_NULL);
            }
            else {
                $statement->bindValue($prefix .$key, $value, PDO::PARAM_STR);
            }
        }
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
     * Fetch array
     *
     * @param string $statement
     * @param array $params
     * @param array $paramTypes
     *
     * @return array|mixed
     *
     * @throws \Exception
     */
    public function fetchArray($statement, array $params = array(), array $paramTypes = array())
    {
        return $this->select($statement, $params, $paramTypes, 'array');
    }

    /**
     * Fetch object
     *
     * @param string $statement
     * @param array $params
     * @param array $paramTypes
     *
     * @return array|mixed
     *
     * @throws \Exception
     */
    public function fetchObject($statement, array $params = array(), array $paramTypes = array())
    {
        return $this->select($statement, $params, $paramTypes, 'object');
    }

    /**
     * Fetch class
     *
     * @param string $statement
     * @param array $params
     * @param array $paramTypes
     * @param null|string $className
     * @param bool $fetchAll
     *
     * @return array|mixed
     *
     * @throws \Exception
     */
    public function fetchClass($statement, array $params = array(), array $paramTypes = array(), $returnType = null, $fetchAll = false)
    {
        if (($this->returnType != 'array') && ($this->returnType != 'object')) {
            $returnType = ($returnType !== null) ? $returnType : $this->returnType;
        }
        else if($returnType === null) {
            throw new \Exception(__d('system', 'No valid Entity Class is given'));
        }

        return $this->select($statement, $params, $paramTypes, $returnType, $fetchAll);
    }

    /**
     * Prepares and executes an SQL query and returns the result as an associative array.
     *
     * @param string $sql    The SQL query.
     * @param array  $params The query parameters.
     * @param array  $types  The query parameter types.
     *
     * @return array
     */
    public function fetchAll($sql, array $params = array(), $paramTypes = array(), $returnType = null)
    {
        return $this->select($sql, $params, $paramTypes, $returnType, true);
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
    public function select($sql, array $params = array(), array $paramTypes = array(), $returnType = null, $fetchAll = false)
    {
        // Append select if it isn't appended.
        if (strtolower(substr($sql, 0, 7)) !== 'select ') {
            $sql = "SELECT " . $sql;
        }

        // What return type? Use default if no return type is given in the call.
        $returnType = $returnType ? $returnType : $this->returnType;

        // Prepare the FetchMethod and check the returnType
        $className = null;

        $fetchMethod = self::getFetchMethod($returnType, $className);

        // Prepare and get statement from PDO.
        $stmt = $this->prepare($sql);

        // Bind the key and values (only if given).
        $this->bindParams($stmt, $params, $paramTypes);

        // Execute, we should capture the status of the result.
        $status = $stmt->execute();

        $this->queryCount++;

        // If failed, return now, and don't continue with fetching.
        if (! $status) {
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
    public function selectOne($sql, array $params = array(), array $paramTypes = array(), $returnType = null)
    {
        return $this->select($sql, $params, $paramTypes, $returnType);
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
    public function selectAll($sql, array $params = array(), array $paramTypes = array(), $returnType = null)
    {
        return $this->select($sql, $params, $paramTypes, $returnType, true);
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
    public function insert($table, array $data, array $paramTypes = array(), $transaction = false, $mode = 'insert')
    {
        $insertId = 0;

        if(($mode != 'insert') && ($mode != 'replace')) {
            throw new \Exception(__d('system', 'Insert Mode must be \'insert\' or \'replace\''));
        }
        else {
            $mode = strtoupper($mode);
        }

        // Check for valid data.
        if (! is_array($data)) {
            throw new \Exception(__d('system', 'Data to insert must be an array of column -> value.'));
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

        $this->bindParams($stmt, $data, $paramTypes);

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
     * Performs the SQL standard for a combined DELETE + INSERT, using PRIMARY and UNIQUE keys to determine which row to replace.
     *
     * @param string $table Table to execute the replace.
     * @param array $data Represents the Record data
     * @param bool $transaction Use PDO Transaction. If one replace will fail we will rollback immediately. Default false.
     * @return int|bool|array Could be false on error, or one single ID replaced.
     *
     * @throws \Exception
     */
    public function replace($table, array $params, array $paramTypes = array(), $transaction = false)
    {
        return $this->insert($table, $params, $paramTypes, $transaction, 'replace');
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
    public function update($table, array $data, $where, array $paramTypes = array())
    {
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
        $whereParams = array();

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

                $whereParams[$key] = $value;
            }
        }
        else if(is_string($where)) {
            $whereDetails = $where;
        }

        // Prepare statement.
        $stmt = $this->prepare("UPDATE $table SET $fieldDetails WHERE $whereDetails");

        // Bind fields
        $this->bindParams($stmt, $data, $paramTypes, ':field_');

        // Bind values
        $this->bindParams($stmt, $whereParams, $paramTypes, ':where_');

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
     * @param array|string $where Use a string or key->value like column->value for where mapping.
     * @return bool|int Row Count, number of deleted rows, or false on failure.
     *
     * @throws \Exception
     */
    public function delete($table, $where, array $paramTypes = array())
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
        if(! empty($bindParams)) {
            $this->bindParams($stmt, $bindParams, $paramTypes);
        }

        // Execute and return if failure.
        $this->queryCount++;

        if (! $stmt->execute()) {
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
    public function rawPrepare($sql, $params = array(), array $paramTypes = array())
    {
        // Prepare and get statement from PDO.
        $stmt = $this->prepare($sql);

        // Bind the key and values (only if given).
        $bindParams = array();

        foreach ($params as $key => $value) {
            if (substr($key, 0, 1) !== ':') {
                $key = ':' .$key;
            }

            $bindParams[$key] = $value;
        }

        // Bind parameters.
        if(! empty($bindParams)) {
            $this->bindParams($stmt, $bindParams, $paramTypes, '');
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
    public function escape($string, $paramType = PDO::PARAM_STR)
    {
        return parent::quote($string, $paramType);
    }

    /**
     * Truncate table
     * @param  string $table table name
     * @return int number of rows affected
     */
    abstract public function truncate($table);

    /**
     * Get the columns names for the specified Database Table.
     *
     * @param  string $table table name
     * @return array  Returns the Database Table fields
     */
    abstract public function listColumns($table);

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
