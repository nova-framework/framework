<?php
/**
 * Model - A simple ORM Model class with no Relations (yet).
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database\ORM;

use Helpers\Inflector;
use Database\ORM\Query;
use Database\Connection;


class Model implements \ArrayAccess
{
    /**
     * The database Connection name.
     *
     * @var string
     */
    protected $connection = 'default';

    /**
     * The table associated with the Model.
     *
     * @var string
     */
    protected $table;

    /**
     * The primary key for the Model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The Model's attributes.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * The Model attribute's original state.
     *
     * @var array
     */
    protected $original = array();

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = array();

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = array('*');

    /**
     * Indicates if all mass assignment is enabled.
     *
     * @var bool
     */
    protected static $unguarded = true;

    /**
     * Indicates if the Model exists.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * The Model Relations with other Models.
     */
    protected $relations = array();

    /**
     * Create a new Model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = array())
    {
        // Adjust the Relations array to permit the storage of associated Models data.
        if(! empty($this->relations)) {
            $this->relations = array_fill_keys($this->relations, null);
        }

        $this->syncOriginal();

        $this->fill($attributes);
    }

    /**
     * Get all of the models from the database.
     *
     * @param  array  $columns
     * @return array
     */
    public static function all($columns = array('*'))
    {
        $instance = new static();

        return $instance->newQuery()->get($columns);
    }

    /**
     * Create a new Model instance, save it, then return the instance.
     *
     * @param  array  $attributes
     * @return static
     */
    public static function create(array $attributes = array())
    {
        $model = new static();

        $model->setRawAttributes($attributes);

        $model->save();

        return $model;
    }

    /**
     * Find a Model by its primary key.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return Model
     */
    public static function find($id, $columns = array('*'))
    {
        $instance = new static();

        return $instance->newQuery()->where($instance->getKeyName(), $id)->first($columns);
    }

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

        // Prepare an Models array.
        $models = array(
            Inflector::tableize($parent),
            Inflector::tableize($related)
        );

        // Sort the Models.
        sort($models);

        return implode('_', $models);
    }

    /**
     * Create a new Pivot model instance.
     *
     * @param  \Database\ORM\Model  $parent
     * @param  array  $attributes
     * @param  string  $table
     * @param  bool  $exists
     * @return \Nova\ORM\\Relation\Pivot
     */
    public function newPivot(Model $parent, array $attributes, $table, $exists)
    {
        return new Pivot($parent, $attributes, $table, $exists);
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
     * @return \Database\ORM\Query
     */
    public static function with($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        $instance = new static();

        return $instance->newQuery()->with($relations);
    }

    /**
     * Set a given attribute on the Model.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function setAttribute($key, $value)
    {
        if ($this->hasSetMutator($key)) {
            $method = 'set' .Inflector::classify($key) .'Attribute';

            call_user_func(array($this, $method), $value);
        } else {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Get an attribute from the Model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $inAttributes = array_key_exists($key, $this->attributes);

        if ($inAttributes || $this->hasGetMutator($key)) {
            return $this->getAttributeValue($key);
        }
    }

    /**
     * Save the Model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save()
    {
        $query = $this->newQuery();

        if ($this->exists) {
            $saved = $this->performUpdate($query);
        } else {
            $saved = $this->performInsert($query);
        }

        if ($saved) {
            $this->syncOriginal();
        }

        return $saved;
    }

    /**
     * Perform a Model update operation.
     *
     * @param  \Database\Query  $query
     * @return bool
     */
    protected function performUpdate(Query $query)
    {
        $dirty = $this->getDirty();

        if (count($dirty) > 0) {
            $this->setKeysForSaveQuery($query)->update($dirty);
        }

        return true;
    }

    /**
     * Perform a model insert operation.
     *
     * @param  \Database\Query  $query
     * @return bool
     */
    protected function performInsert(Query $query)
    {
        $attributes = $this->attributes;

        $keyName = $this->getKeyName();

        $id = $query->insertGetId($attributes);

        $this->setAttribute($keyName, $id);

        $this->exists = true;

        return true;
    }

    /**
     * Delete the model from the database.
     *
     * @return bool|null
     */
    public function delete()
    {
        if ($this->exists) {
            $keyName = $this->getKeyName();

            $this->newQuery()
                ->where($keyName, $this->getKey())
                ->delete();

            $this->exists = false;

            // There is no valid primaryKey anymore.
            unset($this->attributes[$keyName]);
        }

        return true;
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Database\Query  $query
     * @return \Database\Query
     */
    protected function setKeysForSaveQuery(Query $query)
    {
        $query->where($this->getKeyName(), $this->getKeyForSaveQuery());

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @return mixed
     */
    protected function getKeyForSaveQuery()
    {
        if (isset($this->original[$this->getKeyName()])) {
            return $this->original[$this->getKeyName()];
        }

        return $this->getAttribute($this->getKeyName());
    }

    /**
     * Get the primary key for the Model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKey;
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
     * Get the foreign key for the Model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        $tableKey = Inflector::singularize($this->table);

        return $tableKey .'_id';
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
            if (! array_key_exists($key, $this->original) || ($value !== $this->original[$key])) {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

    /**
     * Get a plain attribute.
     *
     * @param  string  $key
     * @return mixed
     */
    protected function getAttributeValue($key)
    {
        $value = $this->getAttributeFromArray($key);

        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $value);
        }

        return $value;
    }

    /**
     * Get an attribute from the $attributes array.
     *
     * @param  string  $key
     * @return mixed
     */
    protected function getAttributeFromArray($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
    }

    /**
     * Get the value of an attribute using its mutator.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return mixed
     */
    protected function mutateAttribute($key, $value)
    {
        $method = 'get' .Inflector::classify($key) .'Attribute';

        return call_user_func(array($this, $method), $value);
    }

    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasSetMutator($key)
    {
        $method = 'set' .Inflector::classify($key) .'Attribute';

        return method_exists($this, $method);
    }

    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasGetMutator($key)
    {
        $method = 'get' .Inflector::classify($key) .'Attribute';

        return method_exists($this, $method);
    }

    /**
     * Sync the original attributes with the current.
     *
     * @return Model
     */
    public function syncOriginal()
    {
        $this->original = $this->attributes;

        return $this;
    }

    /**
     * Fill the Model with an array of attributes.
     *
     * @param  array  $attributes
     * @return Model
     */
    public function fill(array $attributes)
    {
        foreach ($this->fillableFromArray($attributes) as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }

        return $this;
    }

    /**
     * Get the fillable attributes of a given array.
     *
     * @param  array  $attributes
     * @return array
     */
    protected function fillableFromArray(array $attributes)
    {
        if ((count($this->fillable) > 0) && ! static::$unguarded) {
            return array_intersect_key($attributes, array_flip($this->fillable));
        }

        return $attributes;
    }

    /**
     * Determine if the given attribute may be mass assigned.
     *
     * @param  string  $key
     * @return bool
     */
    public function isFillable($key)
    {
        if (static::$unguarded) {
            return true;
        } else if (in_array($key, $this->fillable)) {
            return true;
        } else if ($this->isGuarded($key)) {
            return false;
        }

        return (empty($this->fillable) && ! str_starts_with($key, '_'));
    }

    /**
     * Determine if the given key is guarded.
     *
     * @param  string  $key
     * @return bool
     */
    public function isGuarded($key)
    {
        return (in_array($key, $this->guarded) || ($this->guarded == array('*')));
    }

    /**
     * Get a new Base Query for the Model's table.
     *
     * @return \Database\Query
     */
    public function newBaseQuery()
    {
        $connection = Connection::getInstance($this->connection);

        return $connection->table($this->table);
    }

    /**
     * Get a new Query for the Model's table.
     *
     * @return \Database\ORM\Query
     */
    public function newQuery()
    {
        $query = new Query($this);

        return $query->from($this->table);
    }

    /**
     * Create a new instance of the given Model.
     *
     * @param  array  $attributes
     * @param  bool   $exists
     * @return Model
     */
    public function newInstance($attributes = array(), $exists = false)
    {
        $model = new static((array) $attributes);

        $model->exists = $exists;

        return $model;
    }

    /**
     * Create a new Model instance that is existing.
     *
     * @param  array  $attributes
     * @return Model
     */
    public function newModel($attributes = array())
    {
        $instance = $this->newInstance(array(), true);

        $instance->setRawAttributes((array) $attributes, true);

        return $instance;
    }

    /**
     * Set the array of model attributes. No checking is done.
     *
     * @param  array  $attributes
     * @param  bool   $sync
     * @return void
     */
    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->attributes = $attributes;

        if ($sync) {
            $this->syncOriginal();
        }
    }

    public static function getTable()
    {
        $instance = new static();

        return $instance->table;
    }

    /**
     * Get the database Connection name.
     *
     * @return string
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set the database Connection name.
     *
     * @param  string  $connection
     * @return void
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Convert the Model instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert the Model instance to an array.
     *
     * @return array
     */
    public function toArray($withRelations = true)
    {
        if(! $withRelations) {
            return $this->attributes;
        }

        $attributes = $this->attributes;

        foreach ($this->relations as $key => $value) {
            if ($value instanceof Model) {
                // We have an associated Model.
                $attributes[$key] = $value->toArray(false);
            } else if (is_array($value)) {
                // We have an array of associated Models.
                $attributes[$key] = array();

                foreach ($value as $id => $model) {
                    $attributes[$key][$id] = $model->toArray(false);
                }
            } else if (is_null($value)) {
                // We have an empty relationship.
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->attributes[$key]) || ($this->hasGetMutator($key) && ! is_null($this->getAttributeValue($key)))) {
            return $this->getAttribute($key);
        }

        if($this->exists && array_key_exists($name, $this->relations) && method_exists($this, $name)) {
            $data = $this->relations[$name];

            if(empty($data)) {
                // If the current Relation data is empty, fetch the associated information.
                $relation = call_user_func(array($this, $name));

                $data = $relation->get();
            }

            return $data;
        }
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Determine if an attribute exists on the model.
     *
     * @param  string  $key
     * @return void
     */
    public function __isset($key)
    {
        return (isset($this->attributes[$key]) || ($this->hasGetMutator($key) && ! is_null($this->getAttributeValue($key))));
    }

    /**
     * Unset an attribute on the model.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

    /**
     * Handle dynamic method calls into the Method.
     *
     * @param  string  $method
     * @param  array   $params
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $query = $this->newQuery();

        return call_user_func_array(array($query, $method), $params);
    }

    /**
     * Handle dynamic static method calls into the Method.
     *
     * @param  string  $method
     * @param  array   $params
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = new static();

        return call_user_func_array(array($instance, $method), $params);
    }
}
