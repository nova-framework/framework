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
use Nova\Database\Query\Builder\Facade as QB;

use Nova\ORM\Model;

use \PDO;


class Builder
{
    protected $connection = 'default';

    protected $db = null;

    protected $query = null;

    /**
     * The model being queried.
     *
     * @var \Nova\ORM\Model
     */
    protected $model = null;

    protected $tableName;

    protected $primaryKey;

    protected $fields;

    /**
     * The methods that should be returned from Query Builder.
     *
     * @var array
     */
    protected $passthru = array(
        'insert', 'count', 'getConnection',
    );


    public function __construct(Model $model, $connection = 'default')
    {
        $this->model = $model;

        $this->tableName = $model->getTable();

        $this->primaryKey = $model->getKeyName();

        $this->fields = $model->getTableFields();

        // Setup the Connection instance.
        $this->connection = $connection;

        $this->db = Database::getConnection($connection);

        // Finally, setup the inner Query Builder.
        $this->query = $this->newBaseQuery();
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
    // Query Builder Methods
    //--------------------------------------------------------------------

    public function newBaseQuery()
    {
        $table = $this->getTable();

        $query = $this->db->getQueryBuilder();

        return $query->table($table);
    }

    public function getBaseQuery()
    {
        return $this->query;
    }

    //--------------------------------------------------------------------
    // Finder Methods
    //--------------------------------------------------------------------

    public function find($id)
    {
        // We use an new Query to perform this operation.
        $query = $this->newBaseQuery();

        $result = $query->where($this->primaryKey, $id)->asAssoc()->first();

        if($result !== false) {
            return $this->model->newInstance($result);
        }

        return false;
    }

    public function findBy()
    {
        $params = func_get_args();

        if (empty($params)) {
            throw new \UnexpectedValueException(__d('system', 'Invalid parameters'));
        }

        $query = call_user_func_array(array($this->query, 'where'), $params);

        $result = $query->asAssoc()->first();

        if($result !== false) {
            return $this->model->newInstance($result);
        }

        return false;
    }

    public function findMany($values)
    {
        $query = $this->newBaseQuery();

        $data = $query->whereIn($this->primaryKey, $values)->asAssoc()->get();

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

        $this->query = call_user_func_array(array($this->query, 'where'), $params);

        return $this->findAll();
    }

    public function findAll()
    {
        $data = $this->query->asAssoc()->get();

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
        $data = $this->query->asAssoc()->first();

        if($data !== false) {
            return $this->model->newInstance($data);
        }

        return false;
    }

    public function insert($data)
    {
        $query = $this->newBaseQuery();

        return $query->insert($data);
    }

    public function update($data)
    {
        return $this->query->update($data);
    }

    public function delete($id)
    {
        // We use an new Query to perform this operation.
        $query = $this->newBaseQuery();

        return $query->where($this->primaryKey, $id)->delete();
    }

    public function deleteBy()
    {
        $params = func_get_args();

        if (empty($params)) {
            throw new \UnexpectedValueException(__d('system', 'Invalid parameters'));
        }

        $query = call_user_func_array(array($this->query, 'where'), $params);

        return $query->delete();
    }

    /**
     * Counts number of rows modified by an arbitrary WHERE call.
     * @return INT
     */
    public function countBy()
    {
        $params = func_get_args();

        if (empty($params)) {
            throw new \UnexpectedValueException(__d('system', 'Invalid parameters'));
        }

        // We use an new Query to perform this operation.
        $query = $this->newBaseQuery();

        $query = call_user_func_array(array($query, 'where'), $params);

        return $query->count();
    }

    /**
     * Counts total number of records, disregarding any previous conditions.
     *
     * @return int
     */
    public function countAll()
    {
        // We use an new Query to perform this operation.
        $query = $this->newBaseQuery();

        return $query->count();
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
        // We use an new Query to perform this operation.
        $query = $this->newBaseQuery();

        $query = $query->where($field, $value);

        if ($ignore !== null) {
            $query = $query->where($this->primaryKey, $ignore);
        }

        $result = $query->count();

        return ($result > 0) ? false : true;
    }

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $this->query = call_user_func_array(array($this->query, $method), $parameters);

        return $this;
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

        // Get a QueryBuilder instance.
        $queryBuilder = $this->db->queryBuilder();

        $query = $queryBuilder->table($table);

        if($returnType == 'assoc') {
            return $query->asAssoc();
        } else if($returnType == 'array') {
            return $query->asArray();
        } else if($returnType == 'object') {
            return $query->asObject();
        }

        // By default we fetch into Model.
        $className = $this->model->getClass();

        return $query->asObject($className);
    }

}
