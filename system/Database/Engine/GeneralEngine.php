<?php


namespace Nova\Database\Engine;

/**
 * Interface GeneralEngine
 * For implementing basic SQL language engines
 *
 * @package Nova\Database\Engine
 */
interface GeneralEngine
{
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
    function executeQuery($sql, $bind = array(), $method = null, $class = null);

    /**
     * Execute insert query, will automatically build query for you.
     * You can also give an array as $data, this will try to insert each entry in the array.
     * Not all engine's support this! Check the manual!
     *
     * @param string $table Table to execute the insert.
     * @param array $data Represents one record, could also have multidimensional arrays inside to insert
     *                    multiple rows in one call. The engine must support this! Check manual!
     * @return int|bool
     *
     * @throws \Exception
     */
    function executeInsert($table, $data);

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
    function executeUpdate($table, $data, $where, $limit = 1);

    /**
     * Execute Delete statement, this will automatically build the query for you.
     *
     * @param string $table Table to execute the statement.
     * @param array $where Use key->value like column->value for where mapping.
     * @param int $limit Limit the update statement, not supported by every engine!
     * @return bool
     *
     * @throws \Exception
     */
    function executeDelete($table, $where, $limit = 1);


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
    function rawPrepare($sql, $bind = array(), $method = null, $class = null);
}