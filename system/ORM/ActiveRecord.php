<?php
/**
 * ActiveRecord
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 11th, 2016
 */

namespace Nova\ORM;

use Nova\Helpers\Inflector;
use Nova\Database\Connection;
use Nova\ORM\Expect;
use Nova\ORM\Connection\Wrapper as ConnectionWrapper;

use \PDO;


class ActiveRecord extends ConnectionWrapper
{
    protected $className;

    protected $isNew = true;

    protected $db;

    protected static $cache = array();

    protected $primaryKey = 'id';

    protected $tableName;
    protected $serialize;

    /**
     * The Relations
     */
    public $belongsTo = array();
    public $hasOne    = array();
    public $hasMany   = array();

    /**
     * Temporary select's WHERE attributes.
     */
    protected $tempWheres = array();

    /**
     * Temporary select's ORDER attribute.
     */
    protected $selectOrder = null;

    /**
     * Temporary select's LIMIT attribute.
     */
    protected $selectLimit = null;

    /**
     * Temporary select's OFFSET attribute.
     */
    protected $selectOffset = null;

    /**
     * The Table Metadata
     */
    protected $fields;


    public function __construct(array $data = array(), $linkName = 'default')
    {
        parent::__construct($linkName);

        //
        $this->className = get_class($this);

        if (empty($this->tableName)) {
            $tableName = Inflector::pluralize($className);

            $this->tableName = Inflector::tableize($tableName);
        }

        $this->belongsTo = Expect::expectAssocArray($this->belongsTo);
        $this->hasOne    = Expect::expectAssocArray((array)$this->hasOne);
        $this->hasMany   = Expect::expectAssocArray((array)$this->hasMany);

        $this->serialize = Expect::expectArray($this->serialize);

        // Get the Table Fields.
        if ($this->getCache('$tableFields$') === null) {
            $this->fields = $this->getTableFields($this->table());

            $this->setCache('$tableFields$', $this->fields);
        } else {
            $this->fields = $this->getCache('$tableFields$');
        }

        if(! empty($data)) {
            $this->hydrate($data);

            $this->initObject(true);
        }
    }

    private function initObject($isNew)
    {
        $this->isNew = $isNew;

        if (! $this->isNew) {
            $this->unserializeFields();
        }

        $this->afterLoad();
    }

    private function unserializeFields()
    {
        foreach ((array)$this->serialize as $field) {
            if (! empty($this->$field)) {
                $this->$field = unserialize($this->$field);
            }
        }
    }

    private function serializeFields()
    {
        foreach ((array) $this->serialize as $field) {
            if (! empty($this->$field)) {
                $this->$field = serialize($this->$field);
            }
        }
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    private function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    //--------------------------------------------------------------------
    // Caching Methods
    //--------------------------------------------------------------------

    private function getCache($name)
    {
        $token = $this->className .'_' .$name;

        if (isset(self::$cache[$token])) {
            return self::$cache[$token];
        }

        return null;
    }

    private function setCache($name, $value)
    {
        $token = $this->className .'_' .$name;

        self::$cache[$token] = $value;
    }

    //--------------------------------------------------------------------
    // Magic getter Method
    //--------------------------------------------------------------------

    public function __get($name)
    {
        if ($this->getCache($name) !== null) {
            return $this->getCache($name);
        }
        else if (isset($this->belongsTo[$name])) {
            $value = $this->belongsTo[$name];

            if (strpos($value, ':') !== false) {
                list($key, $className) = explode(':', $value);
            } else {
                $key = $name . '_id';

                $className = $value;
            }

            if (isset($this->$key) && ! empty($this->$key)) {
                $obj = new $className();

                $result = $obj->find(this->$key);

                $this->setCache($name, $result);

                return $obj;
            }
        }
        else if (isset($this->hasOne[$name])) {
            $value = $this->hasOne[$name];

            if (strpos($value, ':') !== false) {
                list($key, $className) = explode(':', $value);
            } else {
                $fieldName = Inflector::singularize($this->tableName);

                $key = $fieldName .'_id';

                $className = $value;
            }

            $obj = new $className();

            $result = $obj->findBy($key, $this->{$this->primaryKey});

            $this->setCache($name, $result);

            return $result;
        }
        else if (isset($this->hasMany[$name])) {
            $value = $this->hasMany[$name];

            if (strpos($value, ':') !== false) {
                list($key, $className) = explode(':', $value);
            } else {
                $fieldName = Inflector::singularize($this->tableName);

                $key = $fieldName . '_id';

                $className = $value;
            }

            $obj = new $className();

            $result = $obj->findManyBy($key, $this->{$this->primaryKey});

            $this->setCache($name, $result);

            return $result;
        }
    }

    //--------------------------------------------------------------------
    // Attributes handling Methods
    //--------------------------------------------------------------------

    public function setAttributes($attributes)
    {
        $this->initWithAssocArray($attributes);
    }

    public function getAttributes()
    {
        $result = array();

        foreach ((array) $this->fields as $key => $value) {
            $result[$key] = $this->$key;
        }

        return $result;
    }

    //--------------------------------------------------------------------
    // CRUD Methods
    //--------------------------------------------------------------------

    /**
     * Getter for the table name.
     *
     * @return string The name of the table used by this class (including the DB_PREFIX).
     */
    public function table()
    {
        return DB_PREFIX .$this->tableName;
    }

    public function find($id)
    {
        $className =& $this->className;

        if (! is_integer($id) || ($id < 1)) {
            throw new \UnexpectedValueException(__d('system', 'Parameter should be an positive Integer'));
        }

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." WHERE " .$this->primaryKey ." = :value";

        $this->lastSqlQuery = $sql;

        $result = $this->select($sql, array('value' => $id));

        if($result !== false) {
            $object = new $className();

            $object->hydrate($result);

            $object->isNew = false;
        }
        else {
            $object = null;
        }

        // Reset the Model State.
        $this->resetState();

        return $object;
    }

    public function findBy()
    {
        $className =& $this->className;

        $bindParams = array();

        // Prepare the WHERE parameters.
        $params = func_get_args();

        $where = $this->setWhere($params);

        $whereStr = Connection::parseWhereConditions($this->wheres(), $bindParams);

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." WHERE $whereStr LIMIT 1";

        $this->lastSqlQuery = $sql;

        $result = $this->select($sql, $bindParams);

        // Reset the Model State.
        $this->resetState();

        if($result !== false) {
            $object = new $className();

            $object->hydrate($result);

            $object->isNew = false;
        }
        else {
            $object = null;
        }

        return $object;
    }

    public function findMany($values)
    {
        $className =& $this->className;

        $bindParams = array();

        if(! is_array($values)) {
            throw new \UnexpectedValueException(__d('dbal', 'Parameter should be an Array'));
        }

        // Prepare the WHERE parameters.
        $this->where($this->primaryKey, $values);

        $whereStr = Connection::parseWhereConditions($this->wheres(), $bindParams);

        // Prepare the ORDER details.
        $orderStr = $this->parseSelectOrder();

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." WHERE $whereStr $orderStr";

        $this->lastSqlQuery = $sql;

        $data = $this->select($sql, $bindParams, true);

        // Reset the Model State.
        $this->resetState();

        if($data === false) {
            return false;
        }

        $result = array();

        foreach($data as $row) {
            $object = new $className();

            $object->hydrate($row);

            $object->isNew = false;

            // Add the current object instance to return list.
            $result[] = $object;
        }

        return $result;
    }

    public function findManyBy()
    {
        // Prepare the WHERE parameters.
        $params = func_get_args();

        $this->setWhere($params);

        return $this->findAll();
    }

    public function findAll()
    {
        $className =& $this->className;

        $bindParams = array();

        // Prepare the WHERE details.
        $whereStr = Connection::parseWhereConditions($this->wheres(), $bindParams);

        $orderStr  = $this->parseSelectOrder();
        $limitStr  = $this->parseSelectLimit();
        $offsetStr = $this->parseSelectOffset();

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." WHERE $whereStr $orderStr $limitStr $offsetStr";

        $this->lastSqlQuery = $sql;

        $data = $this->select($sql, $bindParams, true);

        // Reset the Model State.
        $this->resetState();

        if($data === false) {
            return false;
        }

        $result = array();

        foreach($data as $row) {
            $object = new $className();

            $object->hydrate($row);

            $object->isNew = false;

            // Add the current object instance to return list.
            $result[] = $object;
        }

        return $result;
    }

    public function save()
    {
        $data = array();

        $saveFields =& $this->fields;

        if (! $this->beforeSave()) {
            return;
        }

        $this->serializeFields();

        foreach ($saveFields as $fieldName => $fieldInfo) {
            $data[$fieldName] = $this->$fieldName;
        }

        unset($data[$this->primaryKey]);

        return false;
    }

    //--------------------------------------------------------------------
    // Query Building Methods
    //--------------------------------------------------------------------

    public function where($field, $value = '')
    {
        if(empty($field)) {
            throw new \UnexpectedValueException(__d('system', 'Invalid parameters'));
        }

        $this->tempWheres[$field] = $value;

        return $this;
    }

    /**
     * Limit results
     *
     * @param int $limit
     * @return BaseModel $this
     */
    public function limit($limit)
    {
        if (! is_integer($limit) || ($limit < 0)) {
            throw new \UnexpectedValueException(__d('system', 'Invalid parameter'));
        }

        $this->selectLimit  = $limit;

        return $this;
    }

    /**
     * Offset
     *
     * @param int $offset
     * @return BaseModel $this
     */
    public function offset($offset)
    {
        if (! is_integer($offset) || ($offset < 0)) {
            throw new \UnexpectedValueException(__d('system', 'Invalid parameter'));
        }

        $this->selectOffset = $offset;

        return $this;
    }

    /**
     * Order by
     * @param mixed $order
     * @return BaseModel $this
     */
    public function orderBy($order)
    {
        if(empty($order)) {
            $this->selectOrder = null;
        }
        // Ccheck if the Field contains conditions.
        else if (strpos($order, ' ') !== false) {
            // Simplify the white spaces on Field.
            $order = preg_replace('/\s+/', ' ', trim($order));

            // Explode the field into its components.
            $segments = explode(' ', $order);

            if(count($segments) !== 2) {
                throw new \UnexpectedValueException(__d('system', 'Invalid parameter'));
            }

            $field = $segments[0];
            $sense = strtoupper($segments[1]);

            if(($sense != 'ASC') && ($sense != 'DESC')) {
                throw new \UnexpectedValueException(__d('system', 'Invalid parameter'));
            }

            $this->selectOrder = $field .' ' .$sense;
        }
        else {
            $this->selectOrder = $order;
        }

        return $this;
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
    public function select($sql, $params = array(), $fetchAll = false)
    {
        // Firstly, simplify the white spaces and trim the SQL query.
        $sql = preg_replace('/\s+/', ' ', trim($sql));

        // Prepare the parameter Types.
        $paramTypes = $this->getParamTypes($params);

        return $this->db->select($sql, $params, $paramTypes, 'array', $fetchAll);
    }

    //--------------------------------------------------------------------
    // Internal use Methods
    //--------------------------------------------------------------------

    protected function getParamTypes($params)
    {
        $fields =& $this->fields;

        $result = array();

        foreach($params as $field => $value) {
            if(isset($fields[$field])) {
                $fieldInfo = $fields[$field];

                $result[$field] = ($fieldInfo['type'] == 'int') ? PDO::PARAM_INT : PDO::PARAM_STR;
            }
            // No registered field found? We try to guess then the Type.
            else {
                $result[$field] = is_integer($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            }
        }

        return $result;
    }

    protected function resetState()
    {
        // Reset our select WHEREs
        $this->tempWheres = array();

        // Reset our select ORDER
        $this->selectOrder = null;

        // Reset our select LIMIT
        $this->selectLimit = null;

        // Reset our select OFFSET
        $this->selectOffset = null;
    }

    /**
     * Set where
     * @param array $params
     * @return array
     */
    protected function setWhere(array $params = array())
    {
        if (empty($params)) {
            return $this->tempWheres;
        }

        // Get the WHERE condition.
        $condition = array_shift($params);

        if ($condition == null) {
            // Remove all previous defined conditions from our own WHEREs array, too.
            $this->tempWheres = array();
        } else if (is_array($condition)) {
            // Is given an array of Conditions; merge them into our own WHEREs array.
            $this->tempWheres = array_merge($this->tempWheres, $condition);
        } else if (count($params) == 1) {
            // Store the condition and its value.
            $this->tempWheres[$condition] = array_shift($params);
        } else if (count($params) == 2) {
            $operator = array_shift($params);

            if (! in_array($operator, Connection::$whereOperators, true)) {
                throw new \UnexpectedValueException(__d('system', 'Second parameter is invalid'));
            }

            $condition = sprintf('%s $s ?', $condition, $operator);

            // Store the composed condition and its value.
            $this->tempWheres[$condition] = array_shift($params);
        } else {
            throw new \UnexpectedValueException(__d('system', 'Invalid number of parameters'));
        }

        return $this->tempWheres;
    }

    /**
     * Wheres
     * @return array
     */
    protected function wheres()
    {
        return $this->tempWheres;
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

    //--------------------------------------------------------------------
    // Debug Methods
    //--------------------------------------------------------------------

    public function __toString()
    {
        $result = '';

        // Support for checking if an object is empty
        if ($this->isNew) {
            $isEmpty = true;

            foreach ($this->fields as $fieldName => $fieldInfo) {
                if (! empty($this->$fieldName)) {
                    $isEmpty = false;

                    break;
                }
            }

            if ($isEmpty) {
                return $result; // NOTE: result is an empty string.
            }
        }

        $result = $this->className . " #" . $this->{$this->primaryKey} . "\n";

        foreach ($this->fields as $fieldName => $fieldInfo) {
            $result .= "\t" . ucfirst($fieldName) . ': ' . $this->$fieldName . "\n";
        }

        foreach ($this->hasOne as $fieldName => $className) {
            $result .= "\t" . ucfirst($fieldName) . ": (reference to $className objects)\n";
        }

        foreach ($this->hasMany as $fieldName => $className) {
            $result .= "\t" . ucfirst($fieldName) . ": (reference to $className objects)\n";
        }

        foreach ($this->belongsTo as $fieldName => $className) {
            $result .= "\t" . ucfirst($fieldName) . ": (reference to a $className object)\n";
        }

        return $result;
    }

    public function getObjectVariables()
    {
        $vars = get_object_vars($this);

        unset($vars['db']);

        return $vars;
    }

    public function lastSqlQuery()
    {
        return $this->lastSqlQuery;
    }

    //--------------------------------------------------------------------
    // Overwritable Methods
    //--------------------------------------------------------------------

    public function afterLoad()
    {
        return true;
    }

    public function beforeSave()
    {
        return true;
    }

    public function beforeDestroy()
    {
        return true;
    }

}
