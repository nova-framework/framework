<?php
/**
 * Basic Connection.
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 27th, 2015
 */

namespace Nova\Database;

use Nova\Database\Manager;
use Nova\Database\Statement;
use Nova\Database\QueryBuilder;
use Nova\Config;

use \FluentStructure;
use \Closure;
use \PDO;

/**
 * Abstract Connection
 */
abstract class Connection extends PDO
{
    protected $lastSqlQuery = null;

    public static $whereOperators = array("=", "!=", ">", "<", ">=", "<=", "<>", "LIKE");

    /** @var string Return type. */
    protected $returnType = 'array';

    /** @var array Config from the user's app config. */
    protected $config;

    /** @var int Counting how much queries have been executed in total. */
    protected $queryCount = 0;

    /** @var array Store the tables column details. */
    protected static $tables = array();

     /** @var array Store the executed queries, into Profiling mode. */
    protected $queries = array();

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
    public function __construct($dsn, $config = array(), $options = array())
    {
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

        // Prepare the parameters.
        $username = isset($config['user']) ? $config['user'] : '';
        $password = isset($config['password']) ? $config['password'] : '';

        // Call the PDO constructor.
        parent::__construct($dsn, $username, $password, $options);

        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
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
     * @param null $type
     * @return string
     */
    public function returnType($type = null)
    {
        if ($type === null) {
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
     * Get native connection. Could be PDO.
     * @return PDO
     */
    public function getLink()
    {
        return $this;
    }

    /**
     * Get Fluent Based query builder.
     * @param FluentStructure|null $structure
     * @return \Nova\Database\QueryBuilder
     */
    public function getQueryBuilder(FluentStructure $structure = null)
    {
        //$this->countIncomingQuery();

        return new QueryBuilder($this, $structure);
    }

    /**
     * Get fetch method and validate class if exists.
     *
     * @param int $returnType PDO Fetch Method type
     * @param null|string $fetchClass Reference!
     * @return int fetch method
     * @throws \Exception
     */
    public static function getFetchMethod($returnType, &$fetchClass = null)
    {
        // Prepare the parameters.
        $className = null;

        if ($returnType == 'array') {
            $fetchMethod = PDO::FETCH_ASSOC;
        } else if ($returnType == 'object') {
            $fetchMethod = PDO::FETCH_OBJ;
        } else {
            $fetchMethod = PDO::FETCH_CLASS;

            // Check and setup the className.
            $classPath = str_replace('\\', '/', ltrim($returnType, '\\'));

            if (! preg_match('#^App(?:/Modules/.+)?/Models/Entities/(.*)$#i', $classPath)) {
                throw new \Exception(__d('system', 'No valid Entity Name is given: {0}', $returnType));
            }

            if (! class_exists($returnType)) {
                throw new \Exception(__d('system', 'No valid Entity Class is given: {0}', $returnType));
            }

            $fetchClass = $returnType;
        }

        return $fetchMethod;
    }

    /**
     * Guess parameters types
     * @param array $params
     * @return array types back, will be PDO::PARAM_* values
     */
    public static function getParamTypes(array $params)
    {
        $result = array();

        foreach ($params as $key => $value) {
            if (is_integer($value)) {
                $result[$key] = PDO::PARAM_INT;
            } else if (is_bool($value)) {
                $result[$key] = PDO::PARAM_BOOL;
            } else if (is_null($value)) {
                $result[$key] = PDO::PARAM_NULL;
            } else {
                $result[$key] = PDO::PARAM_STR;
            }
        }

        return $result;
    }

    /**
     * Bind the parameters into the statement
     *
     * @param \PDOStatement $statement
     * @param array $params
     * @param array $paramTypes
     * @param string $prefix
     */
    public function bindParams($statement, array $params, array $paramTypes = array(), $prefix = ':')
    {
        if (empty($params)) {
            return;
        }

        // Bind the key and values (only if given).
        foreach ($params as $key => $value) {
            $bindKey = $prefix .$key;

            if (isset($paramTypes[$key])) {
                $bindType = $paramTypes[$key];
            }
            // No parameter Type found, we try our best of to guess it from the Value.
            else if (is_integer($value)) {
                $bindType = PDO::PARAM_INT;
            } else if (is_bool($value)) {
                $bindType = PDO::PARAM_BOOL;
            } else if (is_null($value)) {
                $bindType = PDO::PARAM_NULL;
            } else {
                $bindType = PDO::PARAM_STR;
            }

            $statement->bindValue($bindKey, $value, $bindType);
        }
    }

    public function query($query, $method = null)
    {
        $start = microtime(true);

        if($method !== null) {
            $result = parent::query($query, $method);
        } else {
            $result = parent::query($query);
        }

        $this->logQuery($query, $start);

        return $result;
    }

    public function exec($query)
    {
        $start = microtime(true);

        // Execute the Query.
        $result = parent::exec($query);

        $this->logQuery($query, $start);

        return $result;
    }

    /**
     * @return Statement
     */
    public function prepare($sql, $options = null)
    {
        if(is_array($options)) {
            $statement = parent::prepare($sql, $options);
        } else {
            $statement = parent::prepare($sql);
        }

        return new Statement($statement, $this);
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
        // Get the current Time.
        $start = microtime(true);

        // We can't fetch class here to stay conform the interface, make it OBJ for this simple query.
        $method = ($this->returnType == 'array') ? PDO::FETCH_ASSOC : PDO::FETCH_OBJ;

        if (! $fetch) {
            $result = $this->exec($sql);
        } else {
            $statement = parent::query($sql, $method);

            $result = $statement->fetchAll();
        }

        $this->logQuery($sql, $start);

        return $result;
    }

    /**
     * Basic execute statement. Only for queries with no binding parameters
     * This method is not SQL Injection safe! Please remember to don't use this with dynamic content!
     * This will only return an array or boolean. Depends on your operation and if fetch is on.
     *
     * @param $sql
     * @param null|int $returnType
     * @return \PDOStatement
     */
    public function rawQuery($sql, $returnType = null, $useLogging = true)
    {
        // Get the current Time.
        $start = microtime(true);

        //
        $this->lastSqlQuery = $sql;

        // What return type? Use default if no return type is given in the call.
        $returnType = $returnType ? $returnType : $this->returnType;

        // We can't fetch class here to stay conform the interface, make it OBJ for this simple query.
        $method = ($returnType == 'array') ? PDO::FETCH_ASSOC : PDO::FETCH_OBJ;

        // We don't want to map in memory an entire Billion Records Table, so we return right on a Statement.
        $result = parent::query($sql, $method);

        if($useLogging) {
            $this->logQuery($sql, $start);
        }

        return $result;
    }

    /**
     * Prepare the query and return a prepared statement.
     * Optional bind is available.
     *
     * @param string $sql Query
     * @param array $params optional binding values
     * @param array $paramTypes Types, optional
     * @return \PDOStatement|mixed
     *
     * @throws \Exception
     */
    public function rawPrepare($sql, $params = array(), array $paramTypes = array())
    {
        // Prepare and get statement from PDO.
        $stmt = $this->prepare($sql);

        if($stmt === null) {
            throw new \Exception('Bad things happen into Heaven');
        }

        // Bind the key and values (only if given).
        $bindParams = array();

        foreach ($params as $field => $value) {
            if (substr($field, 0, 1) === ':') {
                $field = substr($field, 1);
            }

            $bindParams[$field] = $value;
        }

        // Bind parameters.
        $this->bindParams($stmt, $bindParams, $paramTypes);

        return $stmt;
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
     * @param null $returnType
     * @param bool $fetchAll
     * @return array|mixed
     * @throws \Exception
     */
    public function fetchClass($statement, array $params = array(), array $paramTypes = array(), $returnType = null, $fetchAll = false)
    {
        if (($this->returnType != 'array') && ($this->returnType != 'object')) {
            $returnType = ($returnType !== null) ? $returnType : $this->returnType;
        } else if ($returnType === null) {
            throw new \Exception(__d('system', 'No valid Entity Class is given'));
        }

        return $this->select($statement, $params, $paramTypes, $returnType, $fetchAll);
    }

    /**
     * Prepares and executes an SQL query and returns the result as an associative array.
     *
     * @param string $sql The SQL query.
     * @param array $params The query parameters.
     * @param array $paramTypes
     * @param null $returnType
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
     * @param array $params
     * @param array $paramTypes
     * @param null $returnType Customized method for fetching, null for engine default or config default.
     * @param bool $fetchAll Ask the method to fetch all the records or not.
     * @return array|null
     * @throws \Exception
     */
    public function select($sql, array $params = array(), array $paramTypes = array(), $returnType = null, $fetchAll = false)
    {
        // Get the current Time.
        $start = microtime(true);

        // Append select if it isn't appended.
        if (strtolower(substr($sql, 0, 7)) !== 'select ') {
            $sql = "SELECT " . $sql;
        }

        // What return type? Use default if no return type is given in the call.
        $returnType = $returnType ? $returnType : $this->returnType;

        // Prepare the FetchMethod and check the returnType
        $className = null;

        $fetchMethod = self::getFetchMethod($returnType, $className);

        // Execute the Query.
        $stmt = $this->executeQuery($sql, $params, $paramTypes);

        if($stmt === false) {
            return false;
        }

        // Fetch the data.
        $result = false;

        if ($fetchAll) {
            // Continue with fetching all records.
            if ($fetchMethod === PDO::FETCH_CLASS) {
                // Fetch in class
                $result = $stmt->fetchAll($fetchMethod, $className);
            } else {
                $result = $stmt->fetchAll($fetchMethod);
            }
        }
        // Continue with fetching one record.
        else {
            if ($fetchMethod === PDO::FETCH_CLASS) {
                // Fetch in class
                $stmt->setFetchMode($fetchMethod, $className);

                $result = $stmt->fetch();
            } else {
                $result = $stmt->fetch($fetchMethod);
            }
        }

        return $result;
    }

    /**
     * Convenience method for fetching one record.
     *
     * @param string $sql
     * @param array $params
     * @param array $paramTypes
     * @param null $returnType
     * @return array|false|null|object
     */
    public function selectOne($sql, array $params = array(), array $paramTypes = array(), $returnType = null)
    {
        return $this->select($sql, $params, $paramTypes, $returnType);
    }

    /**
     * Convenience method for fetching all records.
     *
     * @param string $sql
     * @param array $params
     * @param array $paramTypes Types of parameters, leave empty to auto detect
     * @param null $returnType
     * @return array|false|null
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
     * @param array $paramTypes Types of parameters, leave empty to auto detect
     * @param bool $transaction Use PDO Transaction. If one insert will fail we will rollback immediately. Default false.
     * @param string $mode Represents the insertion Mode, must be 'insert' or 'replace'
     * @return int|bool|array Could be false on error, or one single id inserted, or an array of inserted id's.
     *
     * @throws \Exception
     */
    public function insert($table, array $data, array $paramTypes = array(), $transaction = false, $mode = 'insert')
    {
        if (($mode != 'insert') && ($mode != 'replace')) {
            throw new \Exception(__d('system', 'Insert Mode must be \'insert\' or \'replace\''));
        } else {
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
        $fieldNames = implode(', ', array_keys($data));
        $fieldValues = ':'.implode(', :', array_keys($data));

        // Prepare the SQL statement.
        $sql = "$mode INTO $table ($fieldNames) VALUES ($fieldValues)";

        // Prepare the paramTypes.
        if(empty($paramTypes)) {
            $paramTypes = $this->getTableBindTypes($table);
        }

        // Execute the Update.
        $result = $this->executeUpdate($sql, $data, $paramTypes);

        if($result === false) {
            $failure = true;
        } else {
            // If no error, capture the last inserted id
            $result = $this->lastInsertId();
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
            $result = false;
        }

        return $result;
    }

    /**
     * Performs the SQL standard for a combined DELETE + INSERT, using PRIMARY and UNIQUE keys to determine which row to replace.
     *
     * @param string $table Table to execute the replace.
     * @param array $params Represents the Record data
     * @param array $paramTypes
     * @param bool $transaction Use PDO Transaction. If one replace will fail we will rollback immediately. Default false.
     * @return array|bool|int Could be false on error, or one single ID replaced.
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
     * @param array $paramTypes Types, empty array for guessing.
     * @return int|bool
     *
     * @throws \Exception
     */
    public function update($table, array $data, array $where, array $paramTypes = array())
    {
        $params = array();

        // Prepare the paramTypes.
        if(empty($paramTypes)) {
            $paramTypes = $this->getTableBindTypes($table);
        }
        
        // Column :bind for auto binding.
        $fieldDetails = '';

        $idx = 0;

        foreach ($data as $key => $value) {
            if ($idx > 0) {
                $fieldDetails .= ', ';
            } else {
                $idx++;
            }

            $bindKey = "field_$key";

            $fieldDetails .= "$key = :$bindKey";

            $params[$bindKey] = $value;

            // Adjust the parameter Type information.
            if(isset($paramTypes[$key]) && ! isset($paramTypes[$bindKey])) {
                $paramTypes[$bindKey] = $paramTypes[$key];
            }
        }

        // Prepare the WHERE conditions.
        $whereParams = array();

        $whereDetails = self::parseWhereConditions($where, $whereParams);

        // Merge the whereParams into Update parameters.
        $params = array_merge($params, $whereParams);

        // Prepare the SQL statement.
        $sql = "UPDATE $table SET $fieldDetails WHERE $whereDetails";

        // Execute the Update and return the result.
        return $this->executeUpdate($sql, $params, $paramTypes);
    }

    /**
     * Execute Delete statement, this will automatically build the query for you.
     *
     * @param string $table Table to execute the statement.
     * @param array|string $where Use a string or key->value like column->value for where mapping.
     * @param array $paramTypes Types, empty array for guessing.
     * @return bool|int Row Count, number of deleted rows, or false on failure.
     *
     * @throws \Exception
     */
    public function delete($table, array $where, array $paramTypes = array())
    {
        $bindParams = array();

        // Prepare the WHERE conditions.
        $whereDetails = self::parseWhereConditions($where, $bindParams);

        // Prepare the paramTypes.
        if(empty($paramTypes)) {
            $paramTypes = $this->getTableBindTypes($table);
        }

        // Prepare the SQL statement.
        $sql = "DELETE FROM $table WHERE $whereDetails";

        // Execute the Update and return the result.
        return $this->executeUpdate($sql, $bindParams, $paramTypes);
    }

    /**
     * A generic Query execution which return \Nova\Database\Statement or false when fail.
     * This method is useful to build the 'select' commands.
     */
    public function executeQuery($query, array $params = array(), array $paramTypes = array())
    {
        if(empty($params)) {
            // No parameters given, so we execute a bare Query.
            return $this->query($query);
        }

        // Prepare the SQL Query.
        $stmt = $this->prepare($query);

        // Execute the Query with parameters binding.
        if(! empty($paramTypes)) {
            // Bind the parameters.
            $this->bindParams($stmt, $params, $paramTypes);

            // Execute and return false if failure.
            $result = $stmt->execute();
        } else {
            $result = $stmt->execute($params);
        }

        if($result !== false) {
            // Return the Statement when succeeded.
            return $stmt;
        }

        return false;
    }

    /**
     * A generic Update execution which return affected rows count or false when fail.
     * This method is useful to build the 'insert', 'update' and 'delete' commands.
     */
    public function executeUpdate($query, array $params = array(), array $paramTypes = array())
    {
        if(empty($params)) {
            // No parameters given, so we execute a bare Query.
            return $this->exec($query);
        }

        // Prepare the SQL Query.
        $stmt = $this->prepare($query);

        // Bind conditions.
        $this->bindParams($stmt, $params, $paramTypes);

        // Execute the Query with parameters binding.
        if(! empty($paramTypes)) {
            // Bind the parameters.
            $this->bindParams($stmt, $params, $paramTypes);

            // Execute and return false if failure.
            $result = $stmt->execute();
        } else {
            $result = $stmt->execute($params);
        }

        if ($result !== false) {
            // Return rowcount when succeeded.
            return $stmt->rowCount();
        }

        return false;
    }

    /**
     * Extract ordered type list from two associate key lists of data and types.
     *
     * @param array $data
     * @param array $types
     *
     * @return array
     */
    private function extractTypeValues(array $data, array $paramTypes)
    {
        $result = array();

        foreach ($data as $key => $_) {
            $result[] = isset($paramTypes[$key]) ? $paramTypes[$key] : PDO::PARAM_STR;
        }

        return $result;
    }

    /**
     * Quote escape a string for using in a query.
     *
     * WARNING: You can better prepare a query and bind values in that way.
     * This method could not be always safe.
     *
     * @param $string String to be escaped
     * @param int $paramType Optional parameter type.
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
     * Executes a function in a transaction.
     *
     * The function gets passed this Connection instance as an (optional) parameter.
     *
     * If an exception occurs during execution of the function or transaction commit,
     * the transaction is rolled back and the exception re-thrown.
     *
     * @param \Closure $closure The function to execute transactionally.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function transactional(Closure $closure)
    {
        $this->beginTransaction();

        try {
            $closure($this);

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();

            throw $e;
        }
    }

    /**
     * Get the columns info for the specified Database Table.
     *
     * @param  string $table table name
     * @return array  Returns the Database Table fields info
     */
    abstract public function getTableFields($table);

    /**
     * Get the columns names and types for the specified Database Table.
     *
     * @param  string $table table name
     * @return array  Returns the Database Table field types
     */
    public function getTableBindTypes($table)
    {
        $fields = $this->getTableFields($table);

        // Prepare the column types list.
        $result = array();

        foreach($fields as $fieldName => $fieldInfo) {
            $result[$fieldName] = ($fieldInfo['type'] == 'int') ? PDO::PARAM_INT : PDO::PARAM_STR;
        }

        return $result;
    }

    /**
     * Parse the where conditions.
     *
     * @param array $where
     * @param $bindParams
     * @return string
     */
    public static function parseWhereConditions(array $where, &$bindParams)
    {
        $result = '';

        $connection = Manager::getConnection();

        // Flag which say when we need to add an AND keyword.
        $idx = 0;

        foreach ($where as $field => $value) {
            if ($idx > 0) {
                // Add the 'AND' keyword for the current condition.
                $result .= ' AND ';
            } else {
                $idx++;
            }

            // Firstly, we need to check if the Field contains conditions.
            if (strpos($field, ' ') !== false) {
                // Simplify the white spaces on Field.
                $field = preg_replace('/\s+/', ' ', trim($field));

                // Explode the field into its components.
                $segments = explode(' ', $field);

                if (count($segments) != 3) {
                    throw new \UnexpectedValueException(__d('system', 'Invalid parameters'));
                }

                $fieldName = $segments[0];
                $operator  = $segments[1];
                $bindName  = $segments[2];

                if (! in_array($operator, self::$whereOperators, true)) {
                    throw new \UnexpectedValueException(__d('system', 'Invalid parameters'));
                }

                if ($bindName == '?') {
                    $result .= "$fieldName $operator :$fieldName";

                    $bindParams[$fieldName] = $value;
                } else {
                    if ((substr($bindName, 0, 1) !== ':') || ! is_array($value)) {
                        throw new \UnexpectedValueException(__d('system', 'Invalid parameters'));
                    }

                    $result .= "$fieldName $operator $bindName";

                    // Extract the Value from the array.
                    $value = $value[$bindName];

                    // Remove first character, aka ':', from bindName.
                    $bindName = substr($bindName, 1);

                    $bindParams[$bindName] = $value;
                }

                continue;
            }

            // Process the condition based on Value type.
            if (is_null($value)) {
                $result .= "$field is NULL";

                continue;
            }

            if (is_array($value)) {
                // We need something like: user_id IN (1, 2, 3)
                $result .= "$field IN (" . implode(', ', array_map(array($connection, 'quote'), $value)) . ")";
            } else {
                $result .= "$field = :$field";
            }

            $bindParams[$field] = $value;
        }

        if(empty($result)) {
            // There are no WHERE conditions, then we make the Database to match all records.
            $result = '1 = 1';
        }

        return $result;
    }

    //--------------------------------------------------------------------
    // Debugging and Profiling Methods
    //--------------------------------------------------------------------

    /**
     * Get total executed queries.
     *
     * @return int
     */
    public function getTotalQueries()
    {
        return $this->queryCount;
    }

    /**
     * Upper the counter.
     */
    public function countIncomingQuery()
    {
        $this->queryCount++;
    }

    /**
     * Log a SQL Query.
     */
    function logQuery($sql, $start = 0, array $params = array())
    {
        $options = Config::get('profiler');

        // Count the current Query.
        $this->queryCount++;

        $this->lastSqlQuery = $sql;

        // Verify if the Forensics are enabled into Configuration.
        if ($options['use_forensics'] == true) {
            $start = ($start > 0) ? intval($start) : microtime(true);

            $time = microtime(true);

            //$time = ($time - $start) * 1000;
            $time = $time - $start;

            $query = array(
                'sql' => $sql,
                'params' => $params,
                'time' => $time
            );

            array_push($this->queries, $query);
        }
    }

    /**
     * Get the executed queries array.
     *
     * @return array
     */
    public function getExecutedQueries()
    {
        return $this->queries;
    }

    /**
     * Get the last executed query.
     *
     * @return array
     */
    public function lastSqlQuery()
    {
        return $this->lastSqlQuery;
    }

}
