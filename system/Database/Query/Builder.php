<?php
/**
 * Query Builder.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 22th, 2016
 *
 * Based on Pixie Query Builder: https://github.com/usmanhalalit/pixie
 */

namespace Nova\Database\Query;

use Nova\Database\Connection;
use Nova\Database\Exception;
use Nova\Database\Manager as Database;

use Nova\Database\Query\Raw;
use Nova\Database\Query\Adapter;
use Nova\Database\Query\Object as QueryObject;
use Nova\Database\Query\Builder\Join as JoinBuilder;
use Nova\Database\Query\Builder\Transaction;
use Nova\Database\Query\Builder\TransactionHaltException;

use PDO;


class Builder
{

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var array
     */
    protected $statements = array();

    /**
     * @var null|\Nova\Database\Statement
     */
    protected $statement = null;

    /**
     * @var null|string
     */
    protected $tablePrefix = null;

    /**
     * @var \Nova\Database\Query\Adapter
     */
    protected $adapterInstance;

    /**
     * The PDO fetch parameters to use
     *
     * @var array
     */
    protected $fetchParameters = array(\PDO::FETCH_OBJ);

    /**
     * The last Statement exection result
     */
    protected $lastResult;


    /**
     * @param null|\Nova\Database\Connection $connection
     *
     * @throws \Nova\Database\Exception
     */
    public function __construct(Connection $connection = null)
    {
        // Setup the Connection.
        if ($connection !== null) {
            $this->connection = $connection;
        } else {
            $this->connection = Database::getConnection();
        }

        // Setup the Table prefix.
        if (isset($this->adapterConfig['prefix'])) {
            $this->tablePrefix = $this->adapterConfig['prefix'];
        } else {
            $this->tablePrefix = DB_PREFIX;
        }

        // Setup the Query Adapter type.
        $this->adapter = $this->connection->getDriverCode();

        // Setup the Query Adapter options.
        $this->adapterConfig = $this->connection->getOptions();

        // Setup Query Adapter instance.
        $className = '\\Nova\\Database\\Query\\Adapter\\' . $this->adapter;

        $this->adapterInstance = new $className($this->connection);

        // Setup the Fetch Mode.
        $returnType = $this->connection->returnType();

        if ($returnType == 'assoc') {
            $this->setFetchMode(PDO::FETCH_ASSOC);
        } else if ($returnType == 'array') {
            $this->setFetchMode(PDO::FETCH_NUM);
        } else if ($returnType == 'object') {
            $this->setFetchMode(PDO::FETCH_OBJ);
        } else {
            $className = $returnType;

            // Check for a valid className.
            $classPath = str_replace('\\', '/', ltrim($className, '\\'));

            if (! preg_match('#^App(?:/Modules/.+)?/Models/(.*)$#i', $classPath)) {
                throw new \Exception(__d('system', 'No valid Model Name is given: {0}', $className));
            }

            if (! class_exists($className)) {
                throw new \Exception(__d('system', 'No valid Model Class is given: {0}', $className));
            }

            $this->setFetchMode(PDO::FETCH_CLASS, $className);
        }
    }

    /**
     * Set the fetch mode
     *
     * @param $mode
     * @return $this
     */
    public function setFetchMode($mode)
    {
        $this->fetchParameters = func_get_args();

        return $this;
    }

    /**
     * Fetch query results as associative array
     *
     * @return \Nova\Database\Query\Builder
     */
    public function asAssoc()
    {
        return $this->setFetchMode(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch query results as array
     *
     * @return \Nova\Database\Query\Builder
     */
    public function asArray()
    {
        return $this->setFetchMode(PDO::FETCH_NUM);
    }

    /**
     * Fetch query results as object of specified type
     *
     * @param $className
     * @param array $constructorArgs
     * @return \Nova\Database\Query\Builder
     */
    public function asObject($className = null, array $constructorArgs = array())
    {
        if($className === null) {
            return $this->setFetchMode(PDO::FETCH_OBJ);
        }

        return $this->setFetchMode(PDO::FETCH_CLASS, $className, $constructorArgs);
    }

    /**
     * @param null|\Nova\Database\Connection $connection
     *
     * @return static
     */
    public function newQuery(Connection $connection = null)
    {
        if (is_null($connection)) {
            $connection = $this->connection;
        }

        return new static($connection);
    }

    /**
     * @param       $sql
     * @param array $bindings
     *
     * @return $this
     */
    public function query($sql, $bindings = array())
    {
        list($this->statement) = $this->statement($sql, $bindings);

        return $this;
    }

    /**
     * This method should be used together with the custom query() command, and it offer
     * the ability to return the last executed statement, for a further processing.
     *
     * @return \Nova\Database\Statement
     */
    public function getStatement()
    {
        return $this->statement;
    }

    /**
     * This method return the result of the last Statement execution.
     *
     * @return boolean
     */
    public function getLastResult()
    {
        return $this->lastResult;
    }

    /**
     * @param       $sql
     * @param array $bindings
     *
     * @return array PDOStatement and execution time as float
     */
    public function statement($sql, $bindings = array())
    {
        $start = microtime(true);

        $statement = $this->connection->prepare($sql);

        foreach ($bindings as $key => $value) {
            $statement->bindValue(
                is_int($key) ? $key + 1 : $key,
                $value,
                (is_int($value) || is_bool($value)) ? PDO::PARAM_INT : PDO::PARAM_STR
            );
        }

        $this->lastResult = $statement->execute();

        return array($statement, microtime(true) - $start);
    }

    /**
     * Get all rows
     *
     * @return \stdClass|null
     */
    public function get()
    {
        $eventResult = $this->fireEvents('before-select');

        if (!is_null($eventResult)) {
            return $eventResult;
        };

        $executionTime = 0;

        if (is_null($this->statement)) {
            $queryObject = $this->getQuery('select');

            list($this->statement, $executionTime) = $this->statement(
                $queryObject->getSql(),
                $queryObject->getBindings()
            );
        }

        $start = microtime(true);

        $result = call_user_func_array(array($this->statement, 'fetchAll'), $this->fetchParameters);

        $executionTime += microtime(true) - $start;

        $this->statement = null;

        $this->fireEvents('after-select', $result, $executionTime);

        return $result;
    }

    /**
     * Get first row
     *
     * @return \stdClass|null
     */
    public function first()
    {
        $this->limit(1);

        $result = $this->get();

        return empty($result) ? null : array_shift($result);
    }

    /**
     * @param        $value
     * @param string $fieldName
     *
     * @return null|\stdClass
     */
    public function findAll($fieldName, $value)
    {
        $this->where($fieldName, '=', $value);

        return $this->get();
    }

    /**
     * @param        $value
     * @param string $fieldName
     *
     * @return null|\stdClass
     */
    public function findMany($values, $fieldName = 'id')
    {
        $this->whereIn($fieldName, $values);

        return $this->get();
    }

    /**
     * @param        $value
     * @param string $fieldName
     *
     * @return null|\stdClass
     */
    public function find($value, $fieldName = 'id')
    {
        $this->where($fieldName, '=', $value);

        return $this->first();
    }

    /**
     * Get count of rows
     *
     * @return int
     */
    public function count()
    {
        // Get the current statements
        $originalStatements = $this->statements;

        unset($this->statements['orderBys']);
        unset($this->statements['limit']);
        unset($this->statements['offset']);

        $count = $this->aggregate('count');

        $this->statements = $originalStatements;

        return $count;
    }

    /**
     * @param $type
     *
     * @return int
     */
    protected function aggregate($type)
    {
        // Get the current selects
        $mainSelects = isset($this->statements['selects']) ? $this->statements['selects'] : null;

        // Replace select with a scalar value like `count`
        $this->statements['selects'] = array($this->raw($type . '(*) as field'));

        $row = $this->get();

        // Set the select as it was
        if ($mainSelects) {
            $this->statements['selects'] = $mainSelects;
        } else {
            unset($this->statements['selects']);
        }

        if (is_array($row[0])) {
            return (int) $row[0]['field'];
        } elseif (is_object($row[0])) {
            return (int) $row[0]->field;
        }

        return 0;
    }

    /**
     * @param string $type
     * @param array  $dataToBePassed
     *
     * @return mixed
     * @throws Exception
     */
    public function getQuery($type = 'select', $dataToBePassed = array())
    {
        $allowedTypes = array(
            'select',
            'insert',
            'insertignore',
            'replace',
            'delete',
            'update',
            'criteriaonly'
        );

        if (! in_array(strtolower($type), $allowedTypes)) {
            throw new Exception($type . ' is not a known type.', 2);
        }

        $queryArr = $this->adapterInstance->$type($this->statements, $dataToBePassed);

        return new QueryObject($queryArr['sql'], $queryArr['bindings'], $this->connection);
    }

    /**
     * @param \Nova\Database\Query\Builder $queryBuilder
     * @param null                $alias
     *
     * @return Raw
     */
    public function subQuery(Builder $queryBuilder, $alias = null)
    {
        $sql = '(' . $queryBuilder->getQuery()->getRawSql() . ')';

        if ($alias) {
            $sql = $sql . ' as ' . $alias;
        }

        return $queryBuilder->raw($sql);
    }

    /**
     * @param $data
     *
     * @return array|string
     */
    private function doInsert($data, $type)
    {
        $eventResult = $this->fireEvents('before-insert');

        if (! is_null($eventResult)) {
            return $eventResult;
        }

        // If first value is not an array, it's not a batch insert
        if (! is_array(current($data))) {
            $queryObject = $this->getQuery($type, $data);

            list($result, $executionTime) = $this->statement($queryObject->getSql(), $queryObject->getBindings());

            $return = ($result->rowCount() === 1) ? $this->connection->lastInsertId() : false;
        } else {
            // Its a batch insert
            $return = array();

            $executionTime = 0;

            foreach ($data as $subData) {
                $queryObject = $this->getQuery($type, $subData);

                list($result, $time) = $this->statement($queryObject->getSql(), $queryObject->getBindings());

                $executionTime += $time;

                if ($result->rowCount() === 1) {
                    $return[] = $this->connection->lastInsertId();
                }
            }
        }

        $this->fireEvents('after-insert', $return, $executionTime);

        return $return;
    }

    /**
     * @param $data
     *
     * @return array|string
     */
    public function insert($data)
    {
        return $this->doInsert($data, 'insert');
    }

    /**
     * @param $data
     *
     * @return array|string
     */
    public function insertIgnore($data)
    {
        return $this->doInsert($data, 'insertignore');
    }

    /**
     * @param $data
     *
     * @return array|string
     */
    public function replace($data)
    {
        return $this->doInsert($data, 'replace');
    }

    /**
     * @param $data
     *
     * @return $this
     */
    public function update($data)
    {
        $eventResult = $this->fireEvents('before-update');

        if (! is_null($eventResult)) {
            return $eventResult;
        }

        $queryObject = $this->getQuery('update', $data);

        list($response, $executionTime) = $this->statement($queryObject->getSql(), $queryObject->getBindings());

        $this->fireEvents('after-update', $queryObject, $executionTime);

        if($this->lastResult !== false) {
            return $response->rowCount();
        }

        return false;
    }

    /**
     * @param $data
     *
     * @return array|string
     */
    public function updateOrInsert($data)
    {
        if ($this->first()) {
            return $this->update($data);
        } else {
            return $this->insert($data);
        }
    }

    /**
     * @param $data
     *
     * @return $this
     */
    public function onDuplicateKeyUpdate($data)
    {
        $this->addStatement('onduplicate', $data);

        return $this;
    }

    /**
     *
     */
    public function delete()
    {
        $eventResult = $this->fireEvents('before-delete');

        if (! is_null($eventResult)) {
            return $eventResult;
        }

        $queryObject = $this->getQuery('delete');

        list($response, $executionTime) = $this->statement($queryObject->getSql(), $queryObject->getBindings());

        $this->fireEvents('after-delete', $queryObject, $executionTime);

        if($this->lastResult !== false) {
            return $response->rowCount();
        }

        return false;
    }

    /**
     * @param $tables Single table or multiple tables as an array or as multiple parameters
     *
     * @return static
     */
    public function table($tables)
    {
        if (! is_array($tables)) {
            // Because a single table is converted to an array anyways, this makes sense.
            $tables = func_get_args();
        }

        $instance = new static($this->connection);

        $tables = $this->addTablePrefix($tables, false);

        $instance->addStatement('tables', $tables);

        return $instance;
    }

    /**
     * @param $tables
     *
     * @return $this
     */
    public function from($tables)
    {
        if (! is_array($tables)) {
            $tables = func_get_args();
        }

        $tables = $this->addTablePrefix($tables, false);

        $this->addStatement('tables', $tables);

        return $this;
    }

    /**
     * @param $fields
     *
     * @return $this
     */
    public function select($fields)
    {
        if (! is_array($fields)) {
            $fields = func_get_args();
        }

        $fields = $this->addTablePrefix($fields);

        $this->addStatement('selects', $fields);

        return $this;
    }

    /**
     * @param $fields
     *
     * @return $this
     */
    public function selectDistinct($fields)
    {
        $this->select($fields);

        $this->addStatement('distinct', true);

        return $this;
    }

    /**
     * @param $field
     *
     * @return $this
     */
    public function groupBy($field)
    {
        $field = $this->addTablePrefix($field);

        $this->addStatement('groupBys', $field);

        return $this;
    }

    /**
     * @param        $fields
     * @param string $defaultDirection
     *
     * @return $this
     */
    public function orderBy($fields, $defaultDirection = 'ASC')
    {
        if (!is_array($fields)) {
            $fields = array($fields);
        }

        foreach ($fields as $key => $value) {
            $field = $key;

            $type = $value;

            if (is_int($key)) {
                $field = $value;

                $type = $defaultDirection;
            }

            if (! $field instanceof Raw) {
                $field = $this->addTablePrefix($field);
            }

            $this->statements['orderBys'][] = compact('field', 'type');
        }

        return $this;
    }

    /**
     * @param $limit
     *
     * @return $this
     */
    public function limit($limit)
    {
        $this->statements['limit'] = $limit;

        return $this;
    }

    /**
     * @param $offset
     *
     * @return $this
     */
    public function offset($offset)
    {
        $this->statements['offset'] = $offset;

        return $this;
    }

    /**
     * @param        $key
     * @param        $operator
     * @param        $value
     * @param string $joiner
     *
     * @return $this
     */
    public function having($key, $operator, $value, $joiner = 'AND')
    {
        $key = $this->addTablePrefix($key);

        $this->statements['havings'][] = compact('key', 'operator', 'value', 'joiner');

        return $this;
    }

    /**
     * @param        $key
     * @param        $operator
     * @param        $value
     *
     * @return $this
     */
    public function orHaving($key, $operator, $value)
    {
        return $this->having($key, $operator, $value, 'OR');
    }

    /**
     * @param $key
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function where($key, $operator = null, $value = null)
    {
        // If two params are given then assume operator is =
        if (func_num_args() == 2) {
            $value = $operator;

            $operator = '=';
        }

        return $this->whereHandler($key, $operator, $value);
    }

    /**
     * @param $key
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orWhere($key, $operator = null, $value = null)
    {
        // If two params are given then assume operator is =
        if (func_num_args() == 2) {
            $value = $operator;

            $operator = '=';
        }

        return $this->whereHandler($key, $operator, $value, 'OR');
    }

    /**
     * @param $key
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function whereNot($key, $operator = null, $value = null)
    {
        // If two params are given then assume operator is =
        if (func_num_args() == 2) {
            $value = $operator;

            $operator = '=';
        }

        return $this->whereHandler($key, $operator, $value, 'AND NOT');
    }

    /**
     * @param $key
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orWhereNot($key, $operator = null, $value = null)
    {
        // If two params are given then assume operator is =
        if (func_num_args() == 2) {
            $value = $operator;

            $operator = '=';
        }
        return $this->whereHandler($key, $operator, $value, 'OR NOT');
    }

    /**
     * @param       $key
     * @param array $values
     *
     * @return $this
     */
    public function whereIn($key, $values)
    {
        return $this->whereHandler($key, 'IN', $values, 'AND');
    }

    /**
     * @param       $key
     * @param array $values
     *
     * @return $this
     */
    public function whereNotIn($key, $values)
    {
        return $this->whereHandler($key, 'NOT IN', $values, 'AND');
    }

    /**
     * @param       $key
     * @param array $values
     *
     * @return $this
     */
    public function orWhereIn($key, $values)
    {
        return $this->whereHandler($key, 'IN', $values, 'OR');
    }

    /**
     * @param       $key
     * @param array $values
     *
     * @return $this
     */
    public function orWhereNotIn($key, $values)
    {
        return $this->whereHandler($key, 'NOT IN', $values, 'OR');
    }

    /**
     * @param $key
     * @param $valueFrom
     * @param $valueTo
     *
     * @return $this
     */
    public function whereBetween($key, $valueFrom, $valueTo)
    {
        return $this->whereHandler($key, 'BETWEEN', array($valueFrom, $valueTo), 'AND');
    }

    /**
     * @param $key
     * @param $valueFrom
     * @param $valueTo
     *
     * @return $this
     */
    public function orWhereBetween($key, $valueFrom, $valueTo)
    {
        return $this->whereHandler($key, 'BETWEEN', array($valueFrom, $valueTo), 'OR');
    }

    /**
     * @param $key
     * @return \Nova\Database\Query\Builder
     */
    public function whereNull($key)
    {
        return $this->whereNullHandler($key);
    }

    /**
     * @param $key
     * @return \Nova\Database\Query\Builder
     */
    public function whereNotNull($key)
    {
        return $this->whereNullHandler($key, 'NOT');
    }

    /**
     * @param $key
     * @return \Nova\Database\Query\Builder
     */
    public function orWhereNull($key)
    {
        return $this->whereNullHandler($key, '', 'or');
    }

    /**
     * @param $key
     * @return \Nova\Database\Query\Builder
     */
    public function orWhereNotNull($key)
    {
        return $this->whereNullHandler($key, 'NOT', 'or');
    }

    protected function whereNullHandler($key, $prefix = '', $operator = '')
    {
        $key = $this->adapterInstance->wrapSanitizer($this->addTablePrefix($key));

        return $this->{$operator . 'Where'}($this->raw("{$key} IS {$prefix} NULL"));
    }

    /**
     * @param        $table
     * @param        $key
     * @param        $operator
     * @param        $value
     * @param string $type
     *
     * @return $this
     */
    public function join($table, $key, $operator = null, $value = null, $type = 'inner')
    {
        if (!$key instanceof \Closure) {
            $key = function ($joinBuilder) use ($key, $operator, $value) {
                $joinBuilder->on($key, $operator, $value);
            };
        }

        // Build a new JoinBuilder class, keep it by reference so any changes made in the closure should reflect here.
        $joinBuilder = new JoinBuilder($this->connection);

        $joinBuilder = & $joinBuilder;

        // Call the closure with our new joinBuilder object
        $key($joinBuilder);

        $table = $this->addTablePrefix($table, false);

        // Get the criteria only query from the joinBuilder object
        $this->statements['joins'][] = compact('type', 'table', 'joinBuilder');

        return $this;
    }

    /**
     * Runs a transaction
     *
     * @param $callback
     *
     * @return $this
     */
    public function transaction(\Closure $callback)
    {
        try {
            // Begin the PDO transaction
            $this->connection->beginTransaction();

            // Get the Transaction class
            $transaction = new Transaction($this->connection);

            // Call closure
            $callback($transaction);

            // If no errors have been thrown or the transaction wasn't completed within
            // the closure, commit the changes
            $this->connection->commit();

            return $this;
        } catch (TransactionHaltException $e) {
            // Commit or rollback behavior has been handled in the closure, so exit
            return $this;
        } catch (\Exception $e) {
            // something happened, rollback changes
            $this->connection->rollBack();

            return $this;
        }
    }

    /**
     * @param      $table
     * @param      $key
     * @param null $operator
     * @param null $value
     *
     * @return $this
     */
    public function leftJoin($table, $key, $operator = null, $value = null)
    {
        return $this->join($table, $key, $operator, $value, 'left');
    }

    /**
     * @param      $table
     * @param      $key
     * @param null $operator
     * @param null $value
     *
     * @return $this
     */
    public function rightJoin($table, $key, $operator = null, $value = null)
    {
        return $this->join($table, $key, $operator, $value, 'right');
    }

    /**
     * @param      $table
     * @param      $key
     * @param null $operator
     * @param null $value
     *
     * @return $this
     */
    public function innerJoin($table, $key, $operator = null, $value = null)
    {
        return $this->join($table, $key, $operator, $value, 'inner');
    }

    /**
     * Add a raw query
     *
     * @param $value
     * @param $bindings
     *
     * @return mixed
     */
    public function raw($value, $bindings = array())
    {
        return new Raw($value, $bindings);
    }

    /**
     * Return Connection instance
     *
     * @return \Nova\Database\Connection
     */
    public function connection()
    {
        return $this->connection;
    }

    /**
     * @param Connection $connection
     *
     * @return $this
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param        $key
     * @param        $operator
     * @param        $value
     * @param string $joiner
     *
     * @return $this
     */
    protected function whereHandler($key, $operator = null, $value = null, $joiner = 'AND')
    {
        $key = $this->addTablePrefix($key);

        $this->statements['wheres'][] = compact('key', 'operator', 'value', 'joiner');

        return $this;
    }

    /**
     * Add table prefix (if given) on given string.
     *
     * @param      $values
     * @param bool $tableFieldMix If we have mixes of field and table names with a "."
     *
     * @return array|mixed
     */
    public function addTablePrefix($values, $tableFieldMix = true)
    {
        if (is_null($this->tablePrefix)) {
            return $values;
        }

        // $value will be an array and we will add prefix to all table names

        // If supplied value is not an array then make it one
        $single = false;

        if (!is_array($values)) {
            $values = array($values);
            // We had single value, so should return a single value
            $single = true;
        }

        $return = array();

        foreach ($values as $key => $value) {
            // It's a raw query, just add it to our return array and continue next
            if ($value instanceof Raw || $value instanceof \Closure) {
                $return[$key] = $value;
                continue;
            }

            // If key is not integer, it is likely a alias mapping,
            // so we need to change prefix target
            $target = &$value;

            if (! is_integer($key)) {
                $target = &$key;
            }

            if (! $tableFieldMix || ($tableFieldMix && strpos($target, '.') !== false)) {
                $target = $this->tablePrefix . $target;
            }

            $return[$key] = $value;
        }

        // If we had single value then we should return a single value (end value of the array)
        return $single ? end($return) : $return;
    }

    /**
     * @param $key
     * @param $value
     */
    protected function addStatement($key, $value)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        if (!array_key_exists($key, $this->statements)) {
            $this->statements[$key] = $value;
        } else {
            $this->statements[$key] = array_merge($this->statements[$key], $value);
        }
    }

    /**
     * @param $event
     * @param $table
     *
     * @return callable|null
     */
    public function getEvent($event, $table = ':any')
    {
        return $this->connection->getEventHandler()->getEvent($event, $table);
    }

    /**
     * @param          $event
     * @param string   $table
     * @param callable $action
     *
     * @return void
     */
    public function registerEvent($event, $table, \Closure $action)
    {
        $table = $table ?: ':any';

        if ($table != ':any') {
            $table = $this->addTablePrefix($table, false);
        }

        return $this->connection->getEventHandler()->registerEvent($event, $table, $action);
    }

    /**
     * @param          $event
     * @param string   $table
     *
     * @return void
     */
    public function removeEvent($event, $table = ':any')
    {
        if ($table != ':any') {
            $table = $this->addTablePrefix($table, false);
        }

        return $this->connection->getEventHandler()->removeEvent($event, $table);
    }

    /**
     * @param      $event
     * @return mixed
     */
    public function fireEvents($event)
    {
        $params = func_get_args();

        array_unshift($params, $this);

        return call_user_func_array(array($this->connection->getEventHandler(), 'fireEvents'), $params);
    }

    /**
     * @return array
     */
    public function getStatements()
    {
        return $this->statements;
    }
}
