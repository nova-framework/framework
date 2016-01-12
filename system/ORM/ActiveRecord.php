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
use Nova\ORM\Expect;
use Nova\ORM\Connection\Wrapper as ConnectionWrapper;


class ActiveRecord extends ConnectionWrapper
{
    protected $isNew = true;

    protected $db;

    protected static $cache = array();

    protected $primaryKey = 'id';

    protected $tableName;
    protected $serialize;

    public $belongsTo = array();
    public $hasOne    = array();
    public $hasMany   = array();

    protected $tempWheres = array();


    public function __construct()
    {
        parent::__construct();

        //
        $className = get_class($this);

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
            $this->fields = $this->getTableFields();

            $this->setCache('$tableFields$', $this->fields);
        } else {
            $this->fields = $this->getCache('$tableFields$');
        }

        // Get the number of arguments.
        $numArgs = func_num_args();

        // Setup the Object according with its arguments.
        if ($numArgs == 1) {
            $arg = func_get_arg(0);

            if (is_array($arg)) {
                $this->initFromArray($arg);
            } else {
                $this->initFromId($arg);
            }
        } else if ($numArgs == 0) {
            $this->isNew = true;
        } else {
            throw new \Exception('Invalid number of arguments to initialization of ' .$className);
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

    private function initFromArray($assocArray)
    {
        $this->initFromAssocArray($assocArray);

        $this->initObject(true);
    }

    private function initFromId($id)
    {
        // TBD

        $this->initObject(false);
    }

    private function initFromAssocArray(array $assocArray)
    {
        foreach ($assocArray as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    //--------------------------------------------------------------------
    // Caching Methods
    //--------------------------------------------------------------------

    private function getCache($name)
    {
        $token = get_class($this) .'_' .$name;

        if (isset(self::$cache[$token])) {
            return self::$cache[$token];
        }

        return null;
    }

    private function setCache($name, $value)
    {
        $token = get_class($this) .'_' .$name;

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
                $obj = new $className($this->$key);

                $this->setCache($name, $obj);

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
    // Finder Methods
    //--------------------------------------------------------------------

    /**
     * Getter for the table name.
     *
     * @return string The name of the table used by this class (without the DB_PREFIX).
     */
    public function table()
    {
        return DB_PREFIX .$this->tableName;
    }

    public function find($id)
    {
        if (! is_integer($id)) {
            throw new \UnexpectedValueException(__d('system', 'Parameter should be an Integer'));
        }

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." WHERE " .$this->primaryKey ." = :value";

        $result = $this->select($sql, array('value' => $id));

        // Reset the Model State.
        $this->resetState();

        return $result;
    }

    public function findBy()
    {
        $bindParams = array();

        // Prepare the WHERE parameters.
        $params = func_get_args();

        $where = $this->setWhere($params);

        $whereStr = $this->parseWheres($this->wheres(), $bindParams);

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." $whereStr LIMIT 1";

        $result = $this->select($sql, $bindParams);

        // Reset the Model State.
        $this->resetState();

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
        $bindParams = array();

        // Prepare the WHERE details.
        $whereStr = $this->parseWheres($this->wheres(), $bindParams);

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." $whereStr";

        $result = $this->select($sql, $bindParams, true);

        // Reset the Model State.
        $this->resetState();

        return $result;
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
        return array();
    }

    protected function resetState()
    {
        // Reset our select WHEREs
        $this->tempWheres = array();
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
