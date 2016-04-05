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

use PDO;


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
     * The used \Nova\Database\Connection name.
     */
    protected $connection = 'default';

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
    protected $table;

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

    /**
     * Serialized attributes.
     */
    protected $serialize = array();

    /*
     * Constructor
     */
    public function __construct($connection = 'default')
    {
        $this->className = get_class($this);

        // Setup the Connection name.
        $this->connection = $connection;

        // Prepare the Table Name only if it is not already specified.
        if (empty($this->table)) {
            // Get the Class name without namespace part.
            $className = class_basename($this->className);

            // Explode the tableized className into segments delimited by '_'.
            $segments = explode('_', Inflector::tableize($className));

            // Replace the last segment with its pluralized variant.
            array_push($segments, Inflector::pluralize(array_pop($segments)));

            // Finally, we recombine the segments, obtaining something like:
            // 'UserProfile' -> 'user_profiles'
            $this->table = implode('_', $segments);
        }

        // Adjust the Relations array to permit the storage of associated Models data.
        if(! empty($this->relations)) {
            $this->relations = array_fill_keys($this->relations, null);
        }

        // Init the Model; exists when it has attributes loaded (via class fetching).
        if(! empty($this->attributes)) {
            $this->initObject(true);
        }
    }

    public function getClass()
    {
        return $this->className;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    /**
     * Getter for the Table name.
     *
     * @return string The name of the table used by this Model (including the DB_PREFIX).
     */
    public function table()
    {
        return DB_PREFIX .$this->table;
    }

    public function setConnection($connection)
    {
        if($connection !== null) {
            $this->connection = $connection;
        }

        return $this;
    }

    /**
     * Getter for the Connection name.
     *
     * @return string The name of the Connection used by this Model.
     */
    public function getConnection()
    {
        return $this->connection;
    }

    protected function initObject($exists = false)
    {
        $this->exists = $exists;

        if($this->exist) {
            // Unserialize the specified fields into 'serialize'.
            foreach ($this->serialize as $fieldName) {
                if (! array_key_exists($fieldName, $this->attributes)) {
                    continue;
                }

                $fieldValue = $this->attributes[$fieldName];

                if(! empty($fieldValue)) {
                    $this->attributes[$fieldName] = unserialize($fieldValue);
                }
            }

            // Sync the original attributes.
            $this->syncOriginal();
        }

        $this->afterLoad();
    }

    public function fill(array $attributes)
    {
        // Skip any protected attributes; the primaryKey is skipped by default.
        $skippedFields = array_merge(
            array($this->primaryKey, $this->createdField, $this->modifiedField),
            $this->protectedFields
        );

        foreach ($attributes as $key => $value) {
            if(! in_array($key, $skippedFields)) {
                $this->setAttribute($key, $value);
            }
        }
    }

    /**
     * Fill the model with an array of attributes. Force mass assignment.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function forceFill(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Save a new Model and return the instance.
     *
     * @param  array  $attributes
     * @return static
     */
    public static function create(array $attributes = array())
    {
        $model = new static();

        $model->setRawAttributes($attributes);

        // Initialize the Model.
        $model->initObject();

        $model->save();

        return $model;
    }

     /**
     * Create a collection of models from plain arrays.
     *
     * @param  array  $items
     * @param  string|null  $connection
     * @return array
     */
    public static function hydrate(array $items, $connection = null)
    {
        $instance = new static();

        if($connection !== null) {
            $instance->setConnection($connection);
        }

        $models = array_map(function ($item) use ($instance) {
            return $instance->newFromBuilder($item);
        }, $items);

        return $models;
    }

    /**
     * Create a new instance of the given Model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = array(), $exists = false)
    {
        $instance = new static();

        $instance->setAttributes((array) $attributes);

        // Initialize the Model.
        $instance->initObject($exists);

        return $instance;
    }

    /**
     * Create a new Model instance that is existing.
     *
     * @param  array  $attributes
     * @param  string|null  $connection
     * @return static
     */
    public function newFromBuilder($attributes = array(), $connection = null)
    {
        $model = $this->newInstance(array(), true);

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->connection);

        return $model;
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->getAttribute($this->primaryKey);
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

    public function getForeignKey()
    {
        $tableKey = Inflector::singularize($this->table);

        return $tableKey .'_id';
    }

    public function toArray()
    {
        $attributes = $this->attributes;

        foreach ($this->relations as $key => $value) {
            if ($value instanceof Model) {
                // We have an associated Model.
                $attributes[$key] = $value->toArray();
            } else if (is_array($value)) {
                // We have an array of associated Models.
                $attributes[$key] = array();

                foreach ($value as $id => $entry) {
                    $attributes[$key][$id] = $entry->toArray();
                }
            } else if (is_null($value)) {
                // We have an empty relationship.
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }

    public function setAttributes($attributes)
    {
        $this->forceFill($attributes);
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Set the array of model attributes. No checking is done.
     *
     * @param  array  $attributes
     * @param  bool  $sync
     * @return $this
     */
    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->attributes = $attributes;

        if ($sync) {
            $this->syncOriginal();
        }

        return $this;
    }

    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

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
        $dirty = array();

        foreach ($this->attributes as $key => $value) {
            if (! array_key_exists($key, $this->original)) {
                $dirty[$key] = $value;
            } else if (($value !== $this->original[$key]) && ! $this->originalIsEquivalent($key)) {
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
    protected function originalIsEquivalent($key)
    {
        $current = $this->attributes[$key];
        $original = $this->original[$key];

        if(is_numeric($current) && is_numeric($original)) {
            return (strcmp((string) $current, (string) $original) === 0);
        }

        return false;
    }

    /**
     * Eager load relations on the Model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function load($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        foreach ($relations as $name) {
            if(array_key_exists($name, $this->relations) && method_exists($this, $name)) {
                $relation = call_user_func(array($this, $name));

                $this->relations[$name] = $relation->get();
            }
        }

        return $this;
    }

    /**
     * Begin querying a Model with eager loading.
     *
     * @param  array|string  $relations
     * @return \Nova\ORM\Builder|static
     */
    public static function with($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        $instance = new static();

        return $instance->newBuilder()->with($relations);
    }

    public function newBuilder()
    {
        return new Builder($this, $this->connection);
    }

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

    protected function beforeDestroy()
    {
        return true;
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

        return call_user_func_array(array($builder, $method), $parameters);
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
        $this->attributes[$key] = $value;
    }

    public function __get($name)
    {
        // If the name is of one of attributes, return its value.
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        if($this->exists && array_key_exists($name, $this->relations) && method_exists($this, $name)) {
            $data =& $this->relations[$name];

            if(empty($data)) {
                // If the current Relation data is empty, fetch the associated information.
                $relation = call_user_func(array($this, $name));

                $data = $relation->get();
            }

            return $data;
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
    // Relation Methods
    //--------------------------------------------------------------------

    protected function belongsTo($className, $foreignKey = null)
    {
        return new BelongsTo($className, $this, $foreignKey);
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
        $joinTable = ($joinTable !== null) ? $joinTable : $this->joiningTable($className);

        return new BelongsToMany($className, $this, $joinTable, $foreignKey, $otherKey);
    }

    protected function joiningTable($className)
    {
        $parent = class_basename($this->className);
        $related = class_basename($className);

        // Prepare an models array.
        $models = array(
            Inflector::tableize($parent),
            Inflector::tableize($related)
        );

        // Sort the models.
        sort($models);

        return implode('_', $models);
    }

    //--------------------------------------------------------------------
    // CRUD Methods
    //--------------------------------------------------------------------

    public function save()
    {
        if (! $this->beforeSave()) {
            return false;
        }

        // Get a new Builder instance.
        $builder = $this->newBuilder();

        // Prepare the Data.
        $data = $this->prepareData($builder);

        $result = false;

        if (! $this->exists) {
            // We are into INSERT mode.
            $insertId = $builder->insert($data);

            if($insertId !== false) {
                // Mark the instance as existing and setup it primary key value.
                $this->exists = true;

                $this->setAttribute($this->primaryKey, $insertId);

                $result = true;
            }
        } else if($this->isDirty()) {
            // When the Model exists and it is dirty, we are into UPDATE mode.
            $result = $builder->updateBy($this->primaryKey, $this->getKey(), $data);

            $result = ($result !== false) ? true : $result;
        }

        if($result) {
            // Sync the original attributes.
            $this->syncOriginal();

            return true;
        }

        return false;
    }

    public function destroy()
    {
        if (! $this->exists || ! $this->beforeDestroy()) {
            return false;
        }

        // Get a new Builder instance.
        $builder = $this->newBuilder();

        $key = $this->primaryKey;

        $result = $builder->deleteBy($key, $this->getKey());

        if($result !== false) {
            $this->exists = false;

            // There is no valid primaryKey anymore.
            unset($this->attributes[$key]);

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
     * @return array An array of name => value pairs containing the data for the Model's fields.
     */
    public function prepareData(Builder $builder)
    {
        $data = array();

        $fields = ! empty($this->fields) ? $this->fields : $builder->getFields();

        // Remove any protected attributes; the primaryKey is skipped by default.
        $skippedFields = array_merge((array) $this->primaryKey, $this->protectedFields);

        $fields = array_diff($fields, $skippedFields);

        // Walk over the defined Table Fields and prepare the data entries.
        foreach ($fields as $fieldName) {
            if(! array_key_exists($fieldName, $this->attributes)) {
                continue;
            }

            $value = $this->attributes[$fieldName];

            if(in_array($fieldName, (array) $this->serialize) && ! empty($value)) {
                $data[$fieldName] = serialize($value);
            } else {
                $data[$fieldName] = $value;
            }
        }

        // Process the timestamps.
        if ($this->timestamps) {
            $timestamps = array($this->createdField, $this->modifiedField);
        } else {
            $timestamps = array();
        }

        foreach($timestamps as $fieldName) {
            if(in_array($fieldName, $fields) && ! array_key_exists($fieldName, $data)) {
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

    public function getDebugInfo()
    {
        // Prepare the Cache token.
        $token = $this->connection .'_' .$this->table;

        // Prepare the Table fields.
        if(! empty($this->fields))  {
            $fields = $this->fields;
        } else if(Builder::hasCached($token)) {
            // There was a Builder instance who use this table.
            $fields = Builder::getCache($token);
        } else {
            $builder = $this->newBuilder();

            $fields = $builder->getFields();
        }

        // There we store the parsed output.
        $result = '';

        // Support for checking if an object is empty
        $isEmpty = true;

        if (! $this->exists) {
            foreach ($fields as $fieldName) {
                if (isset($this->attributes[$fieldName])) {
                    $isEmpty = false;

                    break;
                }
            }
        }

        $result = $this->className .(! empty($this->getKey()) ? " #" . $this->getKey() : "") . "\n";

        $result .= "\tExists: " . ($this->exists ? "YES" : "NO") . "\n\n";

        if (! $this->exists && $isEmpty) {
            return $result;
        }

        foreach ($fields as $fieldName) {
            $result .= "\t" . Inflector::classify($fieldName) . ': ' .var_export($this->getAttribute($fieldName), true) . "\n";
        }

        if(! empty($this->relations)) {
            $result .= "\t\n";

            foreach ($this->relations as $name => $data) {
                $relation = call_user_func(array($this, $name));

                $result .= "\t" .ucfirst($relation->type())  .': ' .$name .' -> ' .$relation->getClass() . "\n";
            }
        }

        return $result;
    }

    public function getObjectVars()
    {
        return get_object_vars($this);
    }

}
