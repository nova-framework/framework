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

use Nova\ORM\Builder;
use Nova\ORM\Relation\HasOne;
use Nova\ORM\Relation\HasMany;
use Nova\ORM\Relation\BelongsTo;
use Nova\ORM\Relation\BelongsToMany;
use Nova\ORM\Relation\Pivot;

use \FluentStructure;
use \FluentPDO;
use \PDO;


class Model
{
    /*
     * There is stored the called Class name.
     */
    protected $className;

    /**
     * The Model's State Management variable.
     */
    protected $exists = false;

    /*
     * The used \Nova\Database\Connection instance.
     */
    protected $db = null;

    /**
     * The Table's Primary Key.
     */
    protected $primaryKey = 'id';

    /**
     * The Table Metadata.
     */
    protected $fields = array();

    /**
     * There we store the Model Attributes (its Data).
     */
    protected $attributes = array();

    /**
     * There we store the original Model Attributes.
     */
    protected $original = array();

    /**
     * The Table name belonging to this Model.
     */
    protected $tableName;

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
     * Whether or not to auto-fill a 'created_at' and 'created_at' fields on inserts.
     *
     * @var boolean
     *
     * @access protected
     */
    protected $timestamps = false;

    /**
     * Field name to use to the created time column in the DB table.
     *
     * @var string
     *
     * @access protected
     */
    protected $createdField = 'created_at';

    /**
     * Field name to use to the modified time column in the DB table.
     *
     * @var string
     *
     * @access protected
     */
    protected $updatedField = 'updated_at';

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
        $this->className = get_class($this);

        if($connection instanceof Connection) {
            $this->db = $connection;
        } else {
            $this->db = Database::getConnection($connection);
        }

        // Setup the Table name, if is empty.
        if (empty($this->tableName)) {
            // Try the best to guess the Table name: User -> users
            $classPath = str_replace('\\', '/', $this->className);

            $name = Inflector::pluralize(basename($classPath));

            $this->tableName = Inflector::tableize($name);
        }

        // Get the Table Fields, if they aren't already specified.
        if(empty($this->fields)) {
            $this->fields = $this->db->getTableFields($this->table());
        }

        $this->initObject();
    }

    public static function fromAssoc(array $attributes, $exists = true)
    {
        $model = new static();

        // Hydrate the Model.
        $model->hydrate($attributes);

        // Initialize the Model.
        $model->initObject($exists);

        return $model;
    }

    public static function fromObject($object, $exists = true)
    {
        $attributes = get_object_vars($object);

        return static::fromAssoc($attributes, $exists);
    }

    protected function initObject($exists = false)
    {
        $this->exists = $exists;

        if($this->exists) {
            // Sync the original attributes.
            $this->syncOriginal();
        }

        $this->afterLoad();
    }

    private function hydrate(array $attributes)
    {
        $this->attributes = array();

        $this->original = array();

        if(empty($attributes)) {
            return;
        }

        foreach ($attributes as $key => $value) {
            if(isset($this->fields[$key])) {
                $this->attributes[$key] = $value;
            }
        }
    }

    public function getTableFields()
    {
        return $this->fields;
    }

    public function setTable($table)
    {
        return $this->tableName = $table;
    }

    public function getTable()
    {
        return $this->tableName;
    }

    /**
     * Getter for the table name.
     *
     * @return string The name of the table used by this class (including the DB_PREFIX).
     */
    public function table()
    {
        return DB_PREFIX .$this->tableName;
    }

    public function getConnection()
    {
        return $this->db;
    }

    //--------------------------------------------------------------------
    // Attributes handling Methods
    //--------------------------------------------------------------------

    /**
     * Get the model's original attribute values.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return array|mixed
     */
    public function getOriginal($key = null, $default = null)
    {
        if($key === null) {
            return $this->original;
        }

        return array_key_exists($key, $this->original) ? $this->original[$key] : $default;
    }

    /**
     * Sync the original attributes with the current.
     *
     * @return $this
     */
    public function syncOriginal()
    {
        $this->original = $this->attributes;

        return $this;
    }

    /**
     * Sync a single original attribute with its current value.
     *
     * @param  string  $attribute
     * @return $this
     */
    public function syncOriginalAttribute($attribute)
    {
        $this->original[$attribute] = $this->attributes[$attribute];

        return $this;
    }

    /**
     * Determine if the model or given attribute(s) have been modified.
     *
     * @param  array|string|null  $attributes
     * @return bool
     */
    public function isDirty($attributes = null)
    {
        $dirty = $this->getDirty();

        if (is_null($attributes)) {
            return count($dirty) > 0;
        }

        if (! is_array($attributes)) {
            $attributes = func_get_args();
        }

        foreach ($attributes as $attribute) {
            if (array_key_exists($attribute, $dirty)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the attributes that have been changed since last sync.
     *
     * @return array
     */
    public function getDirty()
    {
        $dirty = [];

        foreach ($this->attributes as $key => $value) {
            if (! array_key_exists($key, $this->original)) {
                $dirty[$key] = $value;
            } else if (($value !== $this->original[$key]) && ! $this->originalIsNumericallyEquivalent($key)) {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

    /**
     * Determine if the new and old values for a given key are numerically equivalent.
     *
     * @param  string  $key
     * @return bool
     */
    protected function originalIsNumericallyEquivalent($key)
    {
        $current = $this->attributes[$key];

        $original = $this->original[$key];

        return (
            is_numeric($current) &&
            is_numeric($original) &&
            (strcmp((string) $current, (string) $original) === 0)
        );
    }

    //--------------------------------------------------------------------
    // Attributes handling Methods
    //--------------------------------------------------------------------

    public function setAttributes($attributes)
    {
        $this->hydrate($attributes);
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    //--------------------------------------------------------------------
    // Data Conversion Methods
    //--------------------------------------------------------------------

    public function toArray()
    {
        return $this->attributes;
    }

    public function toObject()
    {
        $object = new stdClass();

        foreach ($this->attributes as $key => $value) {
            $object->$key = $value;
        }

        return $object;
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
    }

    public function __get($name)
    {
        $fieldName = Inflector::tableize($name);

        // If the name is of one of attributes, return the Value from attribute.
        if (isset($this->fields[$fieldName])) {
            return $this->getAttribute($fieldName);
        }
        else if(! $this->exists) {
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
        return new HasOne($className, $this, $foreignKey);
    }

    protected function hasMany($className, $foreignKey = null)
    {
        return new HasMany($className, $this, $foreignKey);
    }

    protected function belongsToMany($className, $joinTable = null, $foreignKey = null, $otherKey = null)
    {
        if (is_null($joinTable)) {
            $joinTable = $this->joiningTable($className);
        }

        return new BelongsToMany($className, $this, $joinTable, $foreignKey, $otherKey);
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
    // Pivot Methods
    //--------------------------------------------------------------------

    /**
     * Create a new pivot model instance.
     *
     * @param  \Nova\ORM\Model  $parent
     * @param  array  $attributes
     * @param  string  $table
     * @param  bool  $exists
     * @return \Nova\ORM\\Relation\Pivot
     */
    public function newPivot(Model $parent, array $attributes, $table, $exists)
    {
        return new Pivot($parent, $attributes, $table, $exists);
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

        if (! $this->exists) {
            // We are into INSERT mode.
            $result = $this->db->insert($this->table(), $data, $paramTypes);

            if($result !== false) {
                $this->exists = true;

                $this->setAttribute($this->primaryKey, $result);

                // Sync the original attributes.
                $this->syncOriginal();

                $result = true;
            }
        }
        // If the Object is dirty, we are into UPDATE mode.
        else if($this->isDirty()) {
            $where = array(
                $this->primaryKey => $this->getKey()
            );

            $paramTypes = $this->getParamTypes(array_merge($data, $where));

            $result = $this->db->update($this->table(), $data, $where, $paramTypes);

            if($result !== false) {
                // Sync the original attributes.
                $this->syncOriginal();

                $result = true;
            }
        }

        return $result;
    }

    public function delete()
    {
        if (! $this->exists || ! $this->beforeDelete()) {
            return false;
        }

        // Prepare the WHERE parameters.
        $where = array(
            $this->primaryKey => $this->getKey()
        );

        $paramTypes = $this->getParamTypes($where);

        $result = $this->db->delete($this->table(), $where, $paramTypes);

        if($result !== false) {
            $this->exists = false;

            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------
    // Select Methods
    //--------------------------------------------------------------------

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
    // Internal Methods
    //--------------------------------------------------------------------

    public function getParamTypes($params, $strict = true)
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
            if(! in_array($fieldName, $skippedFields) && array_key_exists($fieldName, $this->attributes)) {
                $data[$fieldName] = $this->attributes[$fieldName];
            }
        }

        if ($this->timestamps === true) {
            // Process the 'created_at' field
            $fieldName = $this->createdField;

            if(isset($this->fields[$fieldName]) && ! array_key_exists($fieldName, $data)) {
                $data[$fieldName] = $this->getDate();
            }

            // Process the 'updated_at' field
            $fieldName = $this->modifiedField;

            if(isset($this->fields[$fieldName])) {
                $data[$fieldName] = $this->getDate();
            }
        }

        return $data;
    }

    /**
     * A utility function to allow child models to use the type of date/time format that they prefer.
     * This is primarily used for setting 'created_at' and 'updated_at' values, but can be used by inheriting classes.
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
    protected function getDate($userDate = null)
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

    public function lastSqlQuery()
    {
        return $this->db->lastSqlQuery();
    }

    public function __toString()
    {
        $result = '';

        // Support for checking if an object is empty
        if (! $this->exists) {
            $isEmpty = true;

            foreach ($this->fields as $fieldName => $fieldInfo) {
                if (! empty($this->$fieldName)) {
                    $isEmpty = false;

                    break;
                }
            }

            if ($isEmpty) {
                //return $result; // NOTE: result is an empty string.
            }
        }

        $result = $this->className . " #" . $this->getKey() . "\n";

        $result .= "\tExists: " . ($this->exists ? "YES" : "NO") . "\n\n";

        foreach ($this->fields as $fieldName => $fieldInfo) {
            $result .= "\t" . Inflector::classify($fieldName) . ': ' .$this->getAttribute($fieldName) . "\n";
        }

        if(! empty($this->relations)) {
            $result .= "\t\n";

            foreach ($this->relations as $name) {
                $relation = call_user_func(array($this, $name));

                $result .= "\t" .ucfirst($relation->type())  .': ' .$name .' -> ' .$relation->getClassName() . "\n";
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
