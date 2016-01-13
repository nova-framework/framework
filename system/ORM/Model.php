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
use Nova\Database\Manager as Database;

use \PDO;


class Model
{
    /*
     * The used \Nova\Database\Connection instance.
     */
    protected $db = null;

    /*
     * Internal static Cache.
     */
    protected static $cache = array();

    /*
     * There is stored the called Class name.
     */
    protected $className;

    /**
     * The Model's State Management variables.
     */
    protected $isNew   = true;
    protected $isDirty = false;

    /**
     * The Table's Primary Key.
     */
    protected $primaryKey = 'id';

    /**
     * The Table Metadata.
     */
    protected $fields;

    /**
     * There we store the Model Attributes (its Data).
     */
    protected $attributes = array();

    /**
     * The Table name belonging to this Model.
     */
    protected $tableName;

    /**
     * The Model Relations with other Models.
     */
    protected $relations = array();

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


    public function __construct(array $data = array(), $linkName = 'default')
    {
        $this->className = get_class($this);

        $this->db = Database::getConnection($linkName);

        // Setup the Table name, if is empty.
        if (empty($this->tableName)) {
            // Try the best to guess the Table name: Member -> members
            $tableName = Inflector::pluralize($className);

            $this->tableName = Inflector::tableize($tableName);
        }

        // Get the Table Fields.
        if ($this->getCache('$tableFields$') === null) {
            $this->fields = $this->getTableFields($this->table());

            $this->setCache('$tableFields$', $this->fields);
        } else {
            $this->fields = $this->getCache('$tableFields$');
        }

        if(! empty($data)) {
            $this->hydrate($data);

            $this->initialize(true);
        }
    }

    private function initialize($isNew = false)
    {
        $this->isNew = $isNew;

        $this->afterLoad();
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getConnection()
    {
        return $this->db;
    }

    public function getTableFields($table)
    {
        return $this->db->getTableFields($table);
    }

    private function hydrate(array $data)
    {
        $this->attributes = array();

        if(empty($data)) {
            return;
        }

        foreach ($data as $key => $value) {
            if(isset($this->fields[$key])) {
                $this->attributes[$key] = $value;
            }
        }
    }

    //--------------------------------------------------------------------
    // Caching Management Methods
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

    private function hasCached($name)
    {
        $token = $this->className .'_' .$name;

        return isset(self::$cache[$token]);
    }

    //--------------------------------------------------------------------
    // Magic Methods
    //--------------------------------------------------------------------

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;

        $this->isDirty = true;
    }

    public function __get($name)
    {
        // If the name is of one of attributes, return the Value from attribute.
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        // If there is something into Cache assigned for this name, return it from.
        if ($this->hasCached($name)) {
            return $this->getCache($name);
        }

        // If there is a Relation defined for this name, process it.
        if (isset($this->relations[$name]) && method_exists($this, $name)) {
            $result = call_user_func(array($this, $name));

            $this->setCache($name, $result);

            return $result;
        }
    }

    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }

    public function __unset($name)
    {
        unset($this->attributes[$name]);
    }

    //--------------------------------------------------------------------
    // Attributes handling Methods
    //--------------------------------------------------------------------

    public function setAttributes($attributes)
    {
        $this->hydrate($attributes);
    }

    public function attributes()
    {
        return $this->attributes;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function attribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    //--------------------------------------------------------------------
    // Relation Methods
    //--------------------------------------------------------------------

    public function belongsTo($className, $otherKey = null)
    {
        if(! class_exists($className)) {
            throw new \Exception(__d('system', 'No valid Class is given: {0}', $className));
        }

        $model = new $className();

        if($otherKey === null) {
            $otherKey = $model->getForeignKey();
        }

        return $model->find($this->attribute($otherKey));
    }

    public function hasOne($className, $foreignKey = null)
    {
        if(! class_exists($className)) {
            throw new \Exception(__d('system', 'No valid Class is given: {0}', $className));
        }

        if($foreignKey === null) {
            $foreignKey = $this->getForeignKey();
        }

        // Get an other Model instance.
        $model = new $className();

        return $model->findBy($foreignKey, $this->attribute($this->primaryKey));
    }

    public function hasMany($className, $foreignKey = null)
    {
        if(! class_exists($className)) {
            throw new \Exception(__d('system', 'No valid Class is given: {0}', $className));
        }

        if($foreignKey === null) {
            $foreignKey = $this->getForeignKey();
        }

        // Get an other Model instance.
        $model = new $className();

        return $model->findManyBy($foreignKey, $this->attribute($this->primaryKey));
    }

    public function getForeignKey()
    {
        $foreign = Inflector::singularize($this->tableName);

        return $foreign .'_id';
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

        $result = $this->select($sql, array('value' => $id));

        if($result !== false) {
            $object = new $className();

            $object->hydrate($result);

            $object->initialize();
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

        $result = $this->select($sql, $bindParams);

        // Reset the Model State.
        $this->resetState();

        if($result !== false) {
            $object = new $className();

            $object->hydrate($result);

            $object->initialize();
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

            $object->initialize();

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

            $object->initialize();

            // Add the current object instance to return list.
            $result[] = $object;
        }

        return $result;
    }

    public function save()
    {
        $data = array();

        if (! $this->beforeSave()) {
            return;
        }

        foreach ($this->fields as $fieldName => $fieldInfo) {
            if(($fieldName != $this->primaryKey) && isset($this->attributes[$fieldName])) {
                $data[$fieldName] = $this->attributes[$fieldName];
            }
        }

        $paramTypes = $this->getParamTypes($data);

        if ($this->isNew) {
            // We are into INSERT mode.
            $insertId = $this->db->insert($this->table(), $data, $paramTypes);

            if($insertId !== false) {
                $this->isNew = false;

                $this->setAttribute($this->primaryKey, $insertId);
            }

            return $insertId;
        }

        // We are into UPDATE mode.
        if($this->isDirty) {
            $where = array(
                $this->primaryKey => $this->attribute($this->primaryKey)
            );

            $paramTypes = $this->getParamTypes(array_merge($data, $where));

            $result = $this->db->update($this->table(), $data, $where, $paramTypes);

            return $result;
        }

        return false;
    }

    public function delete()
    {
        if ($this->isNew || ! $this->beforeDelete()) {
            return false;
        }

        // Prepare the WHERE parameters.
        $where = array(
            $this->primaryKey => $this->attribute($this->primaryKey)
        );

        $paramTypes = $this->getParamTypes($where);

        $result = $this->db->delete($this->table(), $where, $paramTypes);

        $this->isNew = true;

        return $result;
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
        $result = $this->db->delete($this->table(), $where);

        // Reset the Model State.
        $this->resetState();

        return $result;
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

    protected function getParamTypes($params, $strict = true)
    {
        $fields =& $this->fields;

        $result = array();

        foreach($params as $field => $value) {
            if(isset($fields[$field])) {
                $fieldInfo = $fields[$field];

                $result[$field] = ($fieldInfo['type'] == 'int') ? PDO::PARAM_INT : PDO::PARAM_STR;
            }
            // No registered field found? We try to guess then the Type, if we aren't into strict mode.
            else if(! $strict) {
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

        return $result;
    }

    public function getObjectVariables()
    {
        $vars = get_object_vars($this);

        unset($vars['db']);

        unset($vars['lastSqlQuery']);
        unset($vars['tempWheres']);
        unset($vars['selectOrder']);
        unset($vars['selectLimit']);
        unset($vars['selectOffset']);

        return $vars;
    }

    public function lastSqlQuery()
    {
        return $this->db->lastSqlQuery();
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

    public function beforeDelete()
    {
        return true;
    }

}
