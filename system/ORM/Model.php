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
use Nova\ORM\Relation\Joining\Pivot as JoiningPivot;
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
    protected $objects = array();

    /**
     * The type of date/time field used for created_on and modified_on fields.
     * Valid types are: 'int', 'datetime', 'date'
     *
     * @var string
     *
     * @access protected
     */
    protected $dateFormat = 'datetime';

    /**
     * Whether or not to auto-fill a 'created_on' field on inserts.
     *
     * @var boolean
     *
     * @access protected
     */
    protected $autoCreated = true;

    /**
     * Field name to use to the created time column in the DB table.
     *
     * @var string
     *
     * @access protected
     */
    protected $createdField = 'created_on';

    /**
     * Whether or not to auto-fill a 'modified_on' field on updates.
     *
     * @var boolean
     *
     * @access protected
     */
    protected $autoModified = true;

    /**
     * Field name to use to the modified time column in the DB table.
     *
     * @var string
     *
     * @access protected
     */
    protected $modifiedField = 'modified_on';

    /**
     * The Fields who are (un)serialized on-fly.
     */
    protected $serialize = array();

    /**
     * The Model Relations with other Models.
     */
    protected $relations = array();

    /**
     * Protected, non-modifiable attributes.
     */
    protected $protectedFields = array();

    /*
     * Constructor
     */
    public function __construct($connection = 'default')
    {
        parent::__construct($connection);

        $this->initObject(true);
    }

    public static function fromAssoc(array $data, $isNew = true)
    {
        $model = new static();

        // Hydrate the Model.
        $model->hydrate($data);

        // Initialize the Model.
        $model->initObject($isNew);

        return $model;
    }

    public static function fromObject($object, $isNew = true)
    {
        $data = get_object_vars($object);

        return static::fromAssoc($data, $isNew);
    }

    protected function initObject($isNew = false)
    {
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
        if (isset($this->fields[$fieldName])) {
            return $this->attribute($fieldName);
        }
        else if($this->isNew) {
            // No Relations can be called for the new Objects.
            return null;
        }

        // Calculate the Cache Token.
        $token = '__get_' .$name;

        // It there data associated with the Cache token, return it.
        if(isset($this->objects[$token])) {
            return $this->objects[$token];
        }

        // If there is a Relation defined for this name, process it.
        if (in_array($name, $this->relations) && method_exists($this, $name)) {
            $relation = call_user_func(array($this, $name));

            $this->objects[$token] = $relation->get();

            return $this->objects[$token];
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
        return new BelongsTo($className, $this, $otherKey);
    }

    protected function hasOne($className, $foreignKey = null)
    {
        if($foreignKey === null) {
            $foreignKey = $this->getForeignKey();
        }

        return new HasOne($className, $this, $foreignKey);
    }

    protected function hasMany($className, $foreignKey = null)
    {
        if($foreignKey === null) {
            $foreignKey = $this->getForeignKey();
        }

        return new HasMany($className, $this, $foreignKey);
    }

    protected function belongsToMany($className, $joinTable = null, $foreignKey = null, $otherKey = null)
    {
        if (is_null($joinTable)) {
            $joinTable = $this->joiningTable($className);
        }

        if($foreignKey === null) {
            $foreignKey = $this->getForeignKey();
        }

        return new BelongsToMany($className, $this, $joinTable, $foreignKey, $otherKey);
    }

    public function getForeignKey()
    {
        $tableKey = Inflector::singularize($this->tableName);

        return $tableKey .'_id';
    }

    public function newPivot($joinTable, $foreignKey, $otherKey, $otherId)
    {
        return new JoiningPivot($joinTable, $foreignKey, $otherKey, $otherId);
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
        return new Builder($this->className, $this->tableName, $this->primaryKey, $this->fields, $this->db);
    }

    //--------------------------------------------------------------------
    // CRUD Methods
    //--------------------------------------------------------------------

    public function save()
    {
        if (! $this->beforeSave()) {
            return;
        }

        // Prepare the Data.
        $data = $this->prepareData();

        // Default value for result.
        $result = false;

        $paramTypes = $this->getParamTypes($data);

        if ($this->isNew) {
            // We are into INSERT mode.
            $result = $this->db->insert($this->table(), $data, $paramTypes);

            if($result !== false) {
                $this->isNew = false;

                $this->setAttribute($this->primaryKey, $result);

                return true;
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

                return true;
            }
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
            $this->primaryKey => $this->getPrimaryKey()
        );

        $paramTypes = $this->getParamTypes($where);

        $result = $this->db->delete($this->table(), $where, $paramTypes);

        if($result !== false) {
            $this->isNew = true;

            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------
    // Internal Methods
    //--------------------------------------------------------------------

    /**
     * Extracts the Model's fields.
     *
     *
     * @return array An array of name => value pairs containing the data for the Model's fields.
     */
    public function prepareData()
    {
        $data = array();

        $skippedFields = array();

        // The primaryKey is skipped by default.
        $skippedFields = array_merge($skippedFields, (array) $this->primaryKey);

        // Remove any protected attributes.
        $skippedFields = array_merge($skippedFields, $this->protectedFields);

        // Walk over the defined Table Fields and prepare the data entries.
        foreach ($this->fields as $fieldName => $fieldInfo) {
            if(in_array($fieldName, $skippedFields) || ! isset($this->attributes[$fieldName])) {
                continue;
            }

            if(! empty($this->attributes[$fieldName]) && in_array($fieldName, $this->serialize)) {
                // The current is marked as a serialized one.
                $data[$fieldName] = serialize($this->attributes[$fieldName]);
            } else {
                $data[$fieldName] = $this->attributes[$fieldName];
            }
        }

        // created_on

        if ($this->autoCreated === true) {
            $fieldName = $this->createdField;

            if(isset($this->fields[$fieldName]) && ! array_key_exists($fieldName, $data)) {
                $data[$fieldName] = $this->date();
            }
        }

        // modified_on

        if ($this->autoModified === true) {
            $fieldName = $this->modifiedField;

            if(isset($this->fields[$fieldName]) && ! array_key_exists($fieldName, $data)) {
                $data[$fieldName] = $this->date();
            }
        }

        return $data;
    }

    /**
     * A utility function to allow child models to use the type of date/time format that they prefer.
     * This is primarily used for setting created_on and modified_on values, but can be used by inheriting classes.
     *
     * The available time formats are:
     * * 'int'      - Stores the date as an integer timestamp.
     * * 'datetime' - Stores the date and time in the SQL datetime format.
     * * 'date'     - Stores the date (only) in the SQL date format.
     *
     * @param mixed $userDate An optional PHP timestamp to be converted.
     *
     * @access protected
     *
     * @return int|null|string The current/user time converted to the proper format.
     */
    protected function date($userDate = null)
    {
        $curr_date = ! empty($userDate) ? $userDate : time();

        switch ($this->dateFormat) {
            case 'int':
                return $curr_date;
                break;
            case 'datetime':
                return date('Y-m-d H:i:s', $curr_date);
                break;
            case 'date':
                return date('Y-m-d', $curr_date);
                break;
        }
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

                $result .= "\t" .ucfirst($relation->type())  .': ' .$relation->getClassName() . "\n";
            }
        }

        return $result;
    }

    public function getObjectVariables()
    {
        $vars = get_object_vars($this);

        unset($vars['db']);
        unset($vars['cache']);

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
