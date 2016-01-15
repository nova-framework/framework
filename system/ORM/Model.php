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

use Nova\ORM\Relation\HasOne;
use Nova\ORM\Relation\HasMany;
use Nova\ORM\Relation\BelongsTo;
use Nova\ORM\Relation\BelongsToMany;
use Nova\ORM\Builder;
use Nova\ORM\Engine;

use \FluentStructure;
use \FluentPDO;
use \PDO;


class Model extends Engine
{
    /**
     * The Model's State Management variables.
     */
    protected $isNew   = true;
    protected $isDirty = false;

    /**
     * There we store the associated Model instances.
     */
    protected $instances = array();

    /**
     * The Fields who are (un)serialized on-fly.
     */
    protected $serialize = array();

    /**
     * The Model Relations with other Models.
     */
    protected $relations = array();

    /*
     * Constructor
     */
    public function __construct($connection = 'default')
    {
        parent::__construct($connection);
    }

    public static function fromAssoc(array $data)
    {
        $model = new static();

        $model->initObject($data);

        return $model;
    }

    public static function fromObject($object)
    {
        $data = get_object_vars($object);

        return static::fromAssoc($data);
    }

    protected function initObject(array $data = array(), $isNew = false)
    {
        $this->hydrate($data);

        $this->isNew = $isNew;

        if (! $this->isNew) {
            foreach ($this->attributes as $key => &$value) {
                if(! empty($value) && in_array($key, $this->serialize)) {
                    $value = unserialize($value);
                }
            }
        }

        $this->afterLoad();
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
    // Magic Methods
    //--------------------------------------------------------------------

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $builder = $this->newBuilder();

        if(method_exists($builder, $method)) {
            return call_user_func_array(array($builder, $method), $parameters);
        }
    }

    /**
     * Handle dynamic static method calls into the method.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        $instance = new static();

        return call_user_func_array(array($instance, $method), $parameters);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $key = Inflector::tableize($key);

        $this->attributes[$key] = $value;

        $this->isDirty = true;
    }

    public function __get($name)
    {
        $fieldName = Inflector::tableize($name);

        // If the name is of one of attributes, return the Value from attribute.
        if (isset($this->attributes[$fieldName])) {
            return $this->attributes[$fieldName];
        }

        // If there is a Relation defined for this name, process it.
        if (! $this->isNew && in_array($name, $this->relations) && method_exists($this, $name)) {
            $relation = call_user_func(array($this, $name));

            return $relation->get();
        }
    }

    public function __isset($name)
    {
        $name = Inflector::tableize($name);

        return isset($this->attributes[$name]);
    }

    public function __unset($name)
    {
        $name = Inflector::tableize($name);

        unset($this->attributes[$name]);
    }

    //--------------------------------------------------------------------
    // Relation Methods
    //--------------------------------------------------------------------

    protected function belongsTo($className, $otherKey = null)
    {
        $token = md5('belongsTo_' .$className);

        if(! isset($this->instances[$token])) {
            $this->instances[$token] = new BelongsTo($className, $this, $otherKey);
        }

        return $this->instances[$token];
    }

    protected function hasOne($className, $foreignKey = null)
    {
        if($foreignKey === null) {
            $foreignKey = $this->getForeignKey();
        }

        //
        $token = md5('hasOne_' .$className);

        if(! isset($this->instances[$token])) {
            $this->instances[$token] = new HasOne($className, $this, $foreignKey);
        }

        return $this->instances[$token];
    }

    protected function hasMany($className, $foreignKey = null)
    {
        if($foreignKey === null) {
            $foreignKey = $this->getForeignKey();
        }

        //
        $token = md5('hasMany_' .$className);

        if(! isset($this->instances[$token])) {
            $this->instances[$token] = new HasMany($className, $this, $foreignKey);
        }

        return $this->instances[$token];
    }

    protected function belongsToMany($className, $joinTable = null, $foreignKey = null, $otherKey = null)
    {
        if (is_null($joinTable)) {
            $table = $this->joiningTable($className);
        }

        if($foreignKey === null) {
            $foreignKey = $this->getForeignKey();
        }

        //
        $token = md5('belongsToMany_' .$className);

        if(! isset($this->instances[$token])) {
            $this->instances[$token] = new BelongsToMany($className, $this, $joinTable, $foreignKey, $otherKey);
        }

        return $this->instances[$token];
    }

    public function getForeignKey()
    {
        $tableKey = Inflector::singularize($this->tableName);

        return $tableKey .'_id';
    }

    protected function joiningTable($className)
    {
        $origin = basename(str_replace('\\', '/', $this->className));
        $target = basename(str_replace('\\', '/', $className));

        // Prepare an models array.
        $models = array(
            Inflector::tableize($origin),
            Inflector::tableize($target)
        );

        // Sort the models.
        sort($models);

        return implode('_', $models);
    }

    //--------------------------------------------------------------------
    // Builder Methods
    //--------------------------------------------------------------------

    public function newBuilder()
    {
        return new Builder($this->className, $this->tableName, $this->primaryKey, $this->db);
    }

    //--------------------------------------------------------------------
    // CRUD Methods
    //--------------------------------------------------------------------

    public function save()
    {
        $data = array();

        if (! $this->beforeSave()) {
            return;
        }

        foreach ($this->fields as $fieldName => $fieldInfo) {
            if(($fieldName != $this->primaryKey) && isset($this->attributes[$fieldName])) {
                if(! empty($value) && in_array($fieldName, $this->serialize)) {
                    // The current is marked as a serialized one.
                    $data[$fieldName] = serialize($this->attributes[$field]);
                } else {
                    $data[$fieldName] = $this->attributes[$fieldName];
                }
            }
        }

        //
        $result = false;

        $paramTypes = $this->getParamTypes($data);

        if ($this->isNew) {
            // We are into INSERT mode.
            $result = $this->db->insert($this->table(), $data, $paramTypes);

            if($result !== false) {
                $this->isNew = false;

                $this->setAttribute($this->primaryKey, $result);
            }
        }
        // If the Object is dirty, we are into UPDATE mode.
        else if($this->isDirty) {
            $where = array(
                $this->primaryKey => $this->getPrimaryKey()
            );

            $paramTypes = $this->getParamTypes(array_merge($data, $where));

            $result = $this->db->update($this->table(), $data, $where, $paramTypes);

            if($result !== false) {
                $this->isDirty = false;
            }
        }

        return $result;
    }

    public function delete()
    {
        if ($this->isNew || ! $this->beforeDelete()) {
            return false;
        }

        // Prepare the WHERE parameters.
        $where = array(
            $this->primaryKey => $this->getPrimaryKey()
        );

        $paramTypes = $this->getParamTypes($where);

        $result = $this->db->delete($this->table(), $where, $paramTypes);

        $this->isNew = true;

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
            $result .= "\t" . Inflector::classify($fieldName) . ': ' . $this->$fieldName . "\n";
        }

        if(! empty($this->relations)) {
            $result .= "\t\n";

            foreach ($this->relations as $name) {
                $relation = call_user_func(array($this, $name));

                $result .= "\t" .$relation->type()  .': ' .$relation->relatedModel() . "\n";
            }
        }

        return $result;
    }

    public function getObjectVariables()
    {
        $vars = get_object_vars($this);

        unset($vars['db']);

        return $vars;
    }

    //--------------------------------------------------------------------
    // QueryBuilder Methods
    //--------------------------------------------------------------------

    /**
     * Start query builder
     *
     * @param FluentStructure|null $structure
     * @return \Nova\Database\QueryBuilder
     */
    public function getQueryBuilder(FluentStructure $structure = null)
    {
        if ($structure === null) {
            $structure = new FluentStructure($this->primaryKey);
        }

        // Get a QueryBuilder instance.
        return $this->db->getQueryBuilder($structure);
    }

    //--------------------------------------------------------------------
    // Events Management
    //--------------------------------------------------------------------

    /**
     * Triggers a model-specific event and call each of it's Observers.
     *
     * @param string $event The name of the event to trigger.
     * @param mixed  $data  The data to be passed to the callback functions.
     *
     * @return mixed
     */
    public function trigger($event, $data = false)
    {
        if (! isset($this->$event) || ! is_array($this->$event)) {
            if (isset($data['fields'])) {
                return $data['fields'];
            }

            return $data;
        }

        foreach ($this->$event as $method) {
            if (strpos($method, '(') !== false) {
                preg_match('/([a-zA-Z0-9\_\-]+)(\(([a-zA-Z0-9\_\-\., ]+)\))?/', $method, $matches);

                $this->callbackParams = explode(',', $matches[3]);
            }

            $data = call_user_func_array(array($this, $method), array($data));
        }

        // In case no method called or method returned the entire data array, we typically just need the $fields.
        if (isset($data['fields'])) {
            return $data['fields'];
        }

        // A few methods might need to return 'ids'.
        if (isset($data['ids'])) {
            return $data['ids'];
        }

        return $data;
    }

    //--------------------------------------------------------------------
    // Overwritable Methods
    //--------------------------------------------------------------------

    protected function afterLoad()
    {
        return true;
    }

    protected function beforeSave()
    {
        return true;
    }

    protected function beforeDelete()
    {
        return true;
    }

}
