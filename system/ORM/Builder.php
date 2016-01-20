<?php
/**
 * Base
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 14th, 2016
 */

namespace Nova\ORM;

use Nova\Database\Connection;
use Nova\Database\Manager as Database;

use Nova\ORM\Model;
use Nova\ORM\Base as BaseBuilder;

use \FluentStructure;
use \FluentPDO;
use \PDO;


class Builder extends BaseBuilder
{
    protected $connection = 'default';

    protected $db = null;

    /**
     * The model being queried.
     *
     * @var \Nova\ORM\Model
     */
    protected $model = null;

    protected $tableName;

    protected $primaryKey;

    protected $fields;


    public function __construct(Model $model, $connection = 'default')
    {
        $this->model = $model;

        $this->tableName = $model->getTable();

        $this->primaryKey = $model->getKeyName();

        $this->fields = $model->getTableFields();

        // Setup the Connection instance.
        $this->connection = $connection;

        $this->db = Database::getConnection($connection);
    }

    public function getTable()
    {
        return $this->tableName;
    }

    public function table()
    {
        return DB_PREFIX .$this->tableName;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function getLink()
    {
        return $this->db;
    }

    //--------------------------------------------------------------------
    // Finder Methods
    //--------------------------------------------------------------------

    public function find($id)
    {
        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." WHERE " .$this->primaryKey ." = :value";

        $result = $this->select($sql, array('value' => $id), 'array');

        if($result !== false) {
            return $this->model->newInstance($result);
        }

        return false;
    }

    public function findBy()
    {
        $bindParams = array();

        // Prepare the WHERE parameters.
        $params = func_get_args();

        $where = $this->setWhere($params);

        $whereStr = Connection::parseWhereConditions($this->wheres(), $bindParams);

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." WHERE $whereStr LIMIT 1";

        $result = $this->select($sql, $bindParams, 'array');

        // Reset the Model State.
        $this->resetState();

        if($result !== false) {
            return $this->model->newInstance($result);
        }

        return false;
    }

    public function findMany($values)
    {
        $bindParams = array();

        if(! is_array($values)) {
            throw new \UnexpectedValueException(__d('system', 'Parameter should be an Array'));
        }

        // Prepare the WHERE parameters.
        $this->where($this->primaryKey, $values);

        $whereStr = Connection::parseWhereConditions($this->wheres(), $bindParams);

        // Prepare the ORDER details.
        $orderStr = $this->parseSelectOrder();

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." WHERE $whereStr $orderStr";

        $data = $this->select($sql, $bindParams, 'array', true);

        // Reset the Model State.
        $this->resetState();

        if($data === false) {
            return false;
        }

        $result = array();

        foreach($data as $row) {
            $result[] = $this->model->newInstance($row);
        }

        return $result;
    }

    public function findManyBy()
    {
        // Prepare the WHERE parameters.
        $params = func_get_args();

        if (empty($params)) {
            throw new \UnexpectedValueException(__d('system', 'Invalid parameters'));
        }

        $this->setWhere($params);

        return $this->findAll();
    }

    public function findAll()
    {
        $bindParams = array();

        // Prepare the WHERE details.
        $whereStr = Connection::parseWhereConditions($this->wheres(), $bindParams);

        $orderStr  = $this->parseSelectOrder();
        $limitStr  = $this->parseSelectLimit();
        $offsetStr = $this->parseSelectOffset();

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." WHERE $whereStr $orderStr $limitStr $offsetStr";

        $data = $this->select($sql, $bindParams, 'array', true);

        // Reset the Model State.
        $this->resetState();

        if($data === false) {
            return false;
        }

        $result = array();

        foreach($data as $row) {
            $result[] = $this->model->newInstance($row);
        }

        return $result;
    }

    public function first()
    {
        $bindParams = array();

        // Prepare the WHERE details.
        $whereStr = Connection::parseWhereConditions($this->wheres(), $bindParams);

        $orderStr  = $this->parseSelectOrder();
        $offsetStr = $this->parseSelectOffset();

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." WHERE $whereStr $orderStr $offsetStr";

        $data = $this->select($sql, $bindParams, 'array');

        // Reset the Model State.
        $this->resetState();

        if($data !== false) {
            return $this->model->newInstance($data);
        }

        return false;
    }

    /**
     * Execute Select Query, binding values into the $sql Query.
     *
     * @param string $sql
     * @param array $bindParams
     * @param bool $fetchAll Ask the method to fetch all the records or not.
     * @return array|null
     *
     * @throws \Exception
     */
    public function select($sql, $params = array(), $returnType = 'array', $fetchAll = false)
    {
        // Firstly, simplify the white spaces and trim the SQL query.
        $sql = preg_replace('/\s+/', ' ', trim($sql));

        // Prepare the parameter Types.
        $paramTypes = $this->getParamTypes($params);

        return $this->db->select($sql, $params, $paramTypes, $returnType, $fetchAll);
    }

    public function selectOne($sql, $params = array(), $returnType = 'array')
    {
        return $this->db->select($sql, $params, $paramTypes, $returnType);
    }

    public function selectAll($sql, $params = array(), $returnType = 'array')
    {
        return $this->db->select($sql, $params, $paramTypes, $returnType, true);
    }

    public function insert(array $data)
    {
        $paramTypes = $this->getParamTypes($data);

        // Execute the Record insertion.
        return $this->db->insert($this->table(), $data, $paramTypes);
    }

    public function update(array $data, array $where)
    {
        // Prepare the WHERE parameters.
        $params = $this->setWhere($where);

        $paramTypes = $this->getParamTypes(array_merge($data, $params));

        // Execute the Record updating.
        return $this->db->update($this->table(), $data, $params, $paramTypes);
    }

    public function delete($id)
    {
        // Prepare the WHERE parameters.
        $where = array($this->primaryKey => $id);

        $paramTypes = $this->getParamTypes($where);

        // Execute the Record deletetion.
        return $this->db->delete($this->table(), $where, $paramTypes);
    }

    public function deleteBy()
    {
        $params = func_get_args();

        if (empty($params)) {
            throw new \UnexpectedValueException(__d('system', 'Invalid parameters'));
        }

        // Prepare the WHERE parameters.
        $where = $this->setWhere($params);

        $paramTypes = $this->getParamTypes($where);

        // Execute the Record deletetion.
        $result = $this->db->delete($this->table(), $where, $paramTypes);

        // Reset the Model State.
        $this->resetState();

        return $result;
    }

    /**
     * Counts number of rows modified by an arbitrary WHERE call.
     * @return INT
     */
    public function countBy()
    {
        $bindParams = array();

        //
        $params = func_get_args();

        if (empty($params)) {
            throw new \UnexpectedValueException(__d('system', 'Invalid parameters'));
        }

        $where = $this->setWhere($params);

        // Prepare the WHERE details.
        $whereStr = Connection::parseWhereConditions($where, $bindParams);

        $orderStr = $this->parseSelectOrder();
        $limitStr = $this->parseSelectLimit();

        // Prepare the SQL Query.
        $sql = "SELECT COUNT(".$this->primaryKey.") as count FROM " .$this->table() ." $whereStr $orderStr $offsetStr";

        $result = $this->select($sql, $bindParams);

        // Reset the Model State.
        $this->resetState();

        if ($result !== false) {
            return $result['count'];
        }

        return 0;
    }

    /**
     * Counts total number of records, disregarding any previous conditions.
     *
     * @return int
     */
    public function countAll()
    {
        // Prepare the SQL Query.
        $sql = "SELECT COUNT(".$this->primaryKey.") as count FROM " .$this->table();

        $result = $this->select($sql);

        if ($result !== false) {
            return $result['count'];
        }

        return 0;
    }

    /**
     * Checks whether a field/value pair exists within the table.
     *
     * @param string $field The field to search for.
     * @param string $value The value to match $field against.
     * @param string $ignore Optionally, the ignored primaryKey.
     *
     * @return bool TRUE/FALSE
     */
    public function isUnique($field, $value, $ignore = null)
    {
        $bindParams = array("where_$field" => $value);

        //
        $sql = "SELECT " .$this->primaryKey ." FROM " .$this->table() ." WHERE $field = :where_$field";

        if ($ignore !== null) {
            $sql .= " AND " .$this->primaryKey ." != :where_ignore";

            $bindParams['where_ignore'] = $ignore;
        }

        $data = $this->select($sql, $bindParams, true);

        if (is_array($data) && (count($data) == 0)) {
            return true;
        }

        return true;
    }

    //--------------------------------------------------------------------
    // Fetch Methods
    //--------------------------------------------------------------------

    public function fetchWithPivot($pivotTable, $foreignKey, $otherKey, $othereId)
    {
        $table = $this->table();

        $primaryKey = $this->primaryKey;

        $bindParams = array('otherKey' => $othereId);

        $paramTypes = array('otherKey' => is_integer($othereId) ? PDO::PARAM_INT : PDO::PARAM_STR);

        // Prepare the WHERE details.
        $whereStr = Connection::parseWhereConditions($this->wheres(), $bindParams);

        $orderStr  = $this->parseSelectOrder();
        $limitStr  = $this->parseSelectLimit();
        $offsetStr = $this->parseSelectOffset();

        // Build the SQL Query.
        $sql = "
            SELECT
                $table.*
            FROM
                $table, $pivotTable
            WHERE
                $table.$primaryKey = $pivotTable.$foreignKey
            AND
                $pivotTable.$otherKey = :otherKey
            AND
                $whereStr
            $orderStr
            $limitStr
            $offsetStr
        ";

        // Simplify the white spaces.
        $sql = preg_replace('/\s+/', ' ', trim($sql));

        $data = $this->db->select($sql, $bindParams, $paramTypes, 'array', true);

        // Reset the Model State.
        $this->resetState();

        if($data === false) {
            return false;
        }

        $result = array();

        foreach($data as $row) {
            $result[] = $this->model->newInstance($row);
        }

        return $result;
    }

    //--------------------------------------------------------------------
    // QueryBuilder Methods
    //--------------------------------------------------------------------

    /**
     * Build a Select Query.
     * @return \SelectQuery
     *
     * @throws \Exception
     */
    public function query($returnType = null)
    {
        $table = $this->table();

        if($returnType == 'array') {
            $asObject = false;
        }
        else if($returnType == 'object') {
            $asObject = true;
        }
        else {
            $asObject = $this->model->getClass();
        }

        // Get a QueryBuilder instance.
        $queryBuilder = $this->db->queryBuilder();

        // First, check and configure for the 'select' Method.
        return $queryBuilder->from($table)->asObject($asObject);
    }

    //--------------------------------------------------------------------
    // Internal use Methods
    //--------------------------------------------------------------------

    protected function getParamTypes(array $params, $strict = true)
    {
        $fields =& $this->fields;

        $result = array();

        foreach($params as $field => $value) {
            if(isset($fields[$field])) {
                $fieldType = $fields[$field];

                $result[$field] = ($fieldType == 'int') ? PDO::PARAM_INT : PDO::PARAM_STR;
            }
            // No registered field found? We try to guess then the Type, if we aren't into strict mode.
            else if(! $strict) {
                $result[$field] = is_integer($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            }
        }

        return $result;
    }

    protected function parseSelectLimit()
    {
        $result = '';

        $limit =& $this->selectLimit;

        if($limit !== null) {
            $result = 'LIMIT ' .$limit;
        }

        return $result;
    }

    protected function parseSelectOffset()
    {
        $result = '';

        $offset =& $this->selectOffset;

        if($offset !== null) {
            $result = 'OFFSET ' .$offset;
        }

        return $result;
    }

    protected function parseSelectOrder()
    {
        $result = '';

        $orderBy =& $this->selectOrder;

        if($orderBy !== null) {
            $result = 'ORDER BY ' .$orderBy;
        }

        return $result;
    }

}
