<?php
/**
 * Abstract Engine.
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date December 19th, 2015
 */

namespace Nova\Database;

/**
 * Interface Engine
 * @package Nova\Database\Engine
 */
interface Engine
{
    /**
     * Get the name of the driver
     * @return string
     */
    public function getDriverName();

    /**
     * Get driver code, used in config as driver string.
     * @return string
     */
    public function getDriverCode();

    /**
     * Set/Get the fetching return type.
     */
    public function returnType();

    /**
     * Get configuration for instance
     * @return array
     */
    public function getOptions();

    /**
     * Get native connection. Could be \PDO
     * @return mixed|\PDO
     */
    public function getLink();

    /**
     * Get the Last Insert ID.
     *
     * @return int
     */
    public function lastInsertID();

    /**
     * Get total executed queries.
     *
     * @return int
     */
    public function getTotalQueries();



    /** Generic Public Api Commands */

    /**
     * Basic execute statement. Only for queries with no binding parameters
     *
     * @param string $sql
     * @param boolean $fetch
     * @return mixed
     */
    public function raw($sql, $fetch = false);

    public function rawQuery($sql);


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
    public function select($sql, $bindParams = array(), $fetchAll = false, $returnType = null);

    /**
     * Convenience methods for selecting records.
     *
     * @param string $sql
     * @param array $bindParams
     * @param null $returnType Customized method for fetching, null for engine default or config default.
     * @param null $class Class for fetching into classes.
     * @return array|null
     *
     * @throws \Exception
     */
    public function selectOne($sql, $bindParams = array(), $returnType = null);
    public function selectAll($sql, $bindParams = array(), $returnType = null);

    /**
     * Execute insert query, will automatically build query for you.
     * You can also give an array as $data, this will try to insert each entry in the array.
     * Not all engine's support this! Check the manual!
     *
     * @param string $table Table to execute the insert.
     * @param array $data Represents one record, could also have multidimensional arrays inside to insert
     *                    multiple rows in one call. The engine must support this! Check manual!
     * @param bool $transaction
     * @return bool|int
     *
     * @throws \Exception
     */
    public function insert($table, $data, $transaction = false);

    /**
     * Execute insert query, will automatically build query for you.
     * You can also give an array as $data, this will try to insert each entry in the array.
     * Not all engine's support this! Check the manual!
     *
     * @param string $table Table to execute the insert.
     * @param array $data Represents one record, could also have multidimensional arrays inside to insert
     *                    multiple rows in one call. The engine must support this! Check manual!
     * @param bool $transaction
     * @return bool|int
     */
    public function insertBatch($table, $data, $transaction = false);

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
    public function update($table, $data, $where);

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
     * The $where_key should be the name of the column to match the record on.
     * If $where_key == 'title', then each record would be matched on that
     * 'title' value of the array. This does mean that the array key needs
     * to be provided with each row's data.
     *
     * @param  string $table The Table name.
     * @param  array $data An associate array of row data to update.
     * @param  string $where The column name to match on.
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function updateBatch($table, $data, $where);

    /**
     * Execute Delete statement, this will automatically build the query for you.
     *
     * @param string $table Table to execute the statement.
     * @param array|string $where Use a string or key->value like column->value for where mapping.
     * @return bool
     *
     * @throws \Exception
     */
    public function delete($table, $where);

    /**
     * Truncate table
     * @param  string $table table name
     * @return int number of rows affected
     */
    public function truncate($table);

    /**
     * Prepare the query and return a prepared statement.
     * Optional bind is available.
     *
     * @param string $sql Query
     * @param array $bindParams optional binding values
     * @return \PDOStatement|mixed
     *
     * @throws \Exception
     */
    public function rawPrepare($sql, $bindParams = array());

    /**
     * Get the field names for the specified Database Table.
     *
     * @param  string $table table name
     * @return array  Returns the Database Table fields
     */
    public function listFields($table);

}
