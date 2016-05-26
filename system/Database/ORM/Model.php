<?php
/**
 * Model - A simple ORM Model class with no Relations (yet).
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database\ORM;

use Helpers\Inflector;
use Database\Connection;
use Database\Query\Builder as QueryBuilder;
use Database\ORM\Builder;

use Database\ORM\Relations\BelongsTo;
use Database\ORM\Relations\BelongsToMany;
use Database\ORM\Relations\HasOne;
use Database\ORM\Relations\HasMany;
use Database\ORM\Relations\HasManyThrough;
use Database\ORM\Relations\Pivot;
use Database\ORM\Relations\Relation;

use Support\Contracts\ArrayableInterface;
use Support\Contracts\JsonableInterface;

use ArrayAccess;
use DateTime;
use LogicException;
use PDO;


class Model implements ArrayableInterface, JsonableInterface, ArrayAccess
{
    /**
     * The Database Connection name.
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
     * The number of Models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 15;

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
     * The loaded relationships for the model.
     *
     * @var array
     */
    protected $relations = array();

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
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = array();

    /**
     * Create a new Model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = array(), $connection = null)
    {
        if (! is_null($connection)) {
            // Store the requested Connection name.
            $this->connection = $connection;
        }

        $this->fill($attributes);

        $this->syncOriginal();
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
     * @return \Database\ORM\Model|static
     */
    public function newFromBuilder($attributes = array())
    {
        $instance = $this->newInstance(array(), true);

        $instance->setRawAttributes((array) $attributes, true);

        return $instance;
    }

    /**
     * Create a collection of Models from plain arrays.
     *
     * @param  array  $items
     * @param  string  $connection
     * @return array
     */
    public static function hydrate(array $items, $connection = null)
    {
        $models = array();

        foreach ($items as $item) {
            $item = (array) $item;

            $model = $instance->newFromBuilder($item);

            if (! is_null($connection)) {
                $model->setConnection($connection);
            }

            array_push($models, $model);
        }

        return $models;
    }

    /**
     * Create a collection of Models from a raw query.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  string  $connection
     * @return \Database\ORM\Model|static[]
     */
    public static function hydrateRaw($query, $bindings = array(), $connection = null)
    {
        $instance = new static();

        if (! is_null($connection)) {
            $instance->setConnection($connection);
        }

        $items = $instance->getConnection()->select($query, $bindings);

        return static::hydrate($items, $connection);
    }

    /**
     * Create a new Model instance, save it, then return the instance.
     *
     * @param  array  $attributes
     * @return static
     */
    public static function create(array $attributes = array())
    {
        $model = new static($attributes);

        $model->save();

        return $model;
    }

    /**
     * Get the first record matching the attributes or create it.
     *
     * @param  array  $attributes
     * @return \Database\ORM\Model
     */
    public static function firstOrCreate(array $attributes)
    {
        if (! is_null($instance = static::firstByAttributes($attributes))) {
            return $instance;
        }

        return static::create($attributes);
    }

    /**
     * Get the first record matching the attributes or instantiate it.
     *
     * @param  array  $attributes
     * @return \Database\ORM\Model
     */
    public static function firstOrNew(array $attributes)
    {
        if (! is_null($instance = static::firstByAttributes($attributes))) {
            return $instance;
        }

        return new static($attributes);
    }

    /**
     * Get the first model for the given attributes.
     *
     * @param  array  $attributes
     * @return \Database\ORM\Model|null
     */
    protected static function firstByAttributes($attributes)
    {
        $query = static::query();

        foreach ($attributes as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first() ?: null;
    }

    /**
     * Begin querying the Model.
     *
     * @return \Database\ORM\Builder|static
     */
    public static function query()
    {
        $instance = new static();

        return $instance->newQuery();
    }

    /**
     * Begin querying the Model on a given connection.
     *
     * @param  string  $connection
     * @return \Database\ORM\Builder|static
     */
    public static function on($connection = null)
    {
        $instance = new static();

        $instance->setConnection($connection);

        return $instance->newQuery();
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
     * Find a Model by its primary key.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return Model
     */
    public static function find($id, $columns = array('*'))
    {
        $instance = new static();

        return $instance->newQuery()->find($id, $columns);
    }

    /**
     * Find a Model by its primary key or return new static.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return \Database\ORM\Model|static
     */
    public static function findOrNew($id, $columns = array('*'))
    {
        if (! is_null($model = static::find($id, $columns))) return $model;

        return new static($columns);
    }

    /**
     * Find a Model by its primary key or throw an exception.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return \Database\ORM\Model|static
     *
     * @throws \Exception
     */
    public static function findOrFail($id, $columns = array('*'))
    {
        if (! is_null($model = static::find($id, $columns))) return $model;

        throw new \Exception('No query results for Model [' .get_called_class() .']');
    }

    /**
     * Eager load relations on the Model.
     *
     * @param  array|string  $relations
     * @return \Database\ORM\Model
     */
    public function load($relations)
    {
        if (is_string($relations)) $relations = func_get_args();

        $query = $this->newQuery()->with($relations);

        $query->eagerLoadRelations(array($this));

        return $this;
    }

    /**
     * Being querying a Model with eager loading.
     *
     * @param  array|string  $relations
     * @return \Database\ORM\Builder|static
     */
    public static function with($relations)
    {
        if (is_string($relations)) $relations = func_get_args();

        $instance = new static();

        return $instance->newQuery()->with($relations);
    }

    /**
     * Define a one-to-one relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Database\ORM\Relations\HasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related;

        $localKey = $localKey ?: $this->getKeyName();

        return new HasOne($instance->newQuery(), $this, $instance->getTable() .'.' .$foreignKey, $localKey);
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @param  string  $relation
     * @return \Database\ORM\Relations\BelongsTo
     */
    public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)
    {
        // If no relation name was given, we will use this debug backtrace to extract
        // the calling method's name and use that as the relationship name as most
        // of the time this will be what we desire to use for the relationships.
        if (is_null($relation)) {
            list(, $caller) = debug_backtrace(false);

            $relation = $caller['function'];
        }

        // If no foreign key was supplied, we can use a backtrace to guess the proper
        // foreign key name by using the name of the relationship function, which
        // when combined with an "_id" should conventionally match the columns.
        if (is_null($foreignKey)) {
            $foreignKey = snake_case($relation).'_id';
        }

        $instance = new $related;

        // Once we have the foreign key names, we'll just create a new Eloquent query
        // for the related models and returns the relationship instance which will
        // actually be responsible for retrieving and hydrating every relations.
        $query = $instance->newQuery();

        $otherKey = $otherKey ?: $instance->getKeyName();

        return new BelongsTo($query, $this, $foreignKey, $otherKey, $relation);
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Database\ORM\Relations\HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related;

        $localKey = $localKey ?: $this->getKeyName();

        return new HasMany($instance->newQuery(), $this, $instance->getTable() .'.' .$foreignKey, $localKey);
    }

    /**
     * Define a has-many-through relationship.
     *
     * @param  string  $related
     * @param  string  $through
     * @param  string|null  $firstKey
     * @param  string|null  $secondKey
     * @return \Database\ORM\Relations\HasManyThrough
     */
    public function hasManyThrough($related, $through, $firstKey = null, $secondKey = null)
    {
        $through = new $through;

        $firstKey = $firstKey ?: $this->getForeignKey();

        $secondKey = $secondKey ?: $through->getForeignKey();

        return new HasManyThrough(with(new $related)->newQuery(), $this, $through, $firstKey, $secondKey);
    }

    /**
     * Define a many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $table
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @param  string  $relation
     * @return \Database\ORM\Relations\BelongsToMany
     */
    public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
    {
        // If no relationship name was passed, we will pull backtraces to get the
        // name of the calling function. We will use that function name as the
        // title of this relation since that is a great convention to apply.
        if (is_null($relation)) {
            $relation = $this->getBelongsToManyCaller();
        }

        // First, we'll need to determine the foreign key and "other key" for the
        // relationship. Once we have determined the keys we'll make the query
        // instances as well as the relationship instances we need for this.
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related;

        $otherKey = $otherKey ?: $instance->getForeignKey();

        // If no table name was provided, we can guess it by concatenating the two
        // models using underscores in alphabetical order. The two model names
        // are transformed to snake case from their default CamelCase also.
        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }

        // Now we're ready to create a new query builder for the related model and
        // the relationship instances for the relation. The relations will set
        // appropriate query constraint and entirely manages the hydrations.
        $query = $instance->newQuery();

        return new BelongsToMany($query, $this, $table, $foreignKey, $otherKey, $relation);
    }

    /**
     * Get the relationship name of the belongs to many.
     *
     * @return  string
     */
    protected function getBelongsToManyCaller()
    {
        $self = __FUNCTION__;

        $caller = array_first(debug_backtrace(false), function($key, $trace) use ($self)
        {
            $caller = $trace['function'];

            return ( ! in_array($caller, Model::$manyMethods) && $caller != $self);
        });

        return ! is_null($caller) ? $caller['function'] : null;
    }

    /**
     * Get the joining table name for a many-to-many relation.
     *
     * @param  string  $related
     * @return string
     */
    public function joiningTable($related)
    {
        // The joining table name, by convention, is simply the snake cased models
        // sorted alphabetically and concatenated with an underscore, so we can
        // just sort the models and join them together to get the table name.
        $base = Inflector::tableize(class_basename($this));

        $related = Inflector::tableize(class_basename($related));

        $models = array($related, $base);

        // Now that we have the model names in an array we can just sort them and
        // use the implode function to join them together with an underscores,
        // which is typically used by convention within the database system.
        sort($models);

        return strtolower(implode('_', $models));
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
     * Destroy the Models for the given IDs.
     *
     * @param  array|int  $ids
     * @return int
     */
    public static function destroy($ids)
    {
        $count = 0;

        $ids = is_array($ids) ? $ids : func_get_args();

        $instance = new static;

        $key = $instance->getKeyName();

        foreach ($instance->whereIn($key, $ids)->get() as $model) {
            if ($model->delete()) $count++;
        }

        return $count;
    }

    /**
     * Delete the Model from the database.
     *
     * @return bool|null
     */
    public function delete()
    {
        if ($this->exists) {
            $query = $this->newQuery();

            $query->where($this->getKeyName(), $this->getKey())->delete();

            $this->exists = false;
        }

        return true;
    }

    /**
     * Increment a Column's value by a given amount.
     *
     * @param  string  $column
     * @param  int     $amount
     * @return int
     */
    protected function increment($column, $amount = 1)
    {
        return $this->incrementOrDecrement($column, $amount, 'increment');
    }

    /**
     * Decrement a Column's value by a given amount.
     *
     * @param  string  $column
     * @param  int     $amount
     * @return int
     */
    protected function decrement($column, $amount = 1)
    {
        return $this->incrementOrDecrement($column, $amount, 'decrement');
    }

    /**
     * Run the increment or decrement method on the Model.
     *
     * @param  string  $column
     * @param  int     $amount
     * @param  string  $method
     * @return int
     */
    protected function incrementOrDecrement($column, $amount, $method)
    {
        $query = $this->newQuery();

        if ( ! $this->exists) {
            return $query->{$method}($column, $amount);
        }

        return $query->where($this->getKeyName(), $this->getKey())->{$method}($column, $amount);
    }

    /**
     * Update the Model in the database.
     *
     * @param  array  $attributes
     * @return mixed
     */
    public function update(array $attributes = array())
    {
        if ( ! $this->exists) {
            return $this->newQuery()->update($attributes);
        }

        return $this->fill($attributes)->save();
    }

    /**
     * Save the model and all of its relationships.
     *
     * @return bool
     */
    public function push()
    {
        if ( ! $this->save()) return false;

        // To sync all of the relationships to the database, we will simply spin through
        // the relationships and save each model via this "push" method, which allows
        // us to recurs into all of these nested relations for this model instance.
        foreach ($this->relations as $models) {
            foreach (Collection::make($models) as $model) {
                if ( ! $model->push()) return false;
            }
        }

        return true;
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
     * @param  \Database\ORM\Builder  $query
     * @return bool
     */
    protected function performUpdate(Builder $query)
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
     * @param  \Database\ORM\Builder  $query
     * @return bool
     */
    protected function performInsert(Builder $query)
    {
        $attributes = $this->attributes;

        $keyName = $this->getKeyName();

        $id = $query->insertGetId($attributes);

        $this->setAttribute($keyName, $id);

        $this->exists = true;

        return true;
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Database\ORM\Builder  $query
     * @return \Database\ORM\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
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

    public function getTable()
    {
       if (isset($this->table)) return $this->table;

       $baseName = Inflector::pluralize(class_basename($this));

       return str_replace('\\', '', Inflector::tableize($baseName));
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

    /**
     * Get the table qualified key name.
     *
     * @return string
     */
    public function getQualifiedKeyName()
    {
        return $this->getTable() .'.' .$this->getKeyName();
    }

    /**
     * Get the number of models to return per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Set the number of Models to return per page.
     *
     * @param  int   $perPage
     * @return void
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
    }

    /**
     * Get the default foreign key name for the Model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return Inflector::tableize(class_basename($this)).'_id';
    }

    /**
     * Get the fillable attributes for the model.
     *
     * @return array
     */
    public function getFillable()
    {
        return $this->fillable;
    }

    /**
     * Set the fillable attributes for the model.
     *
     * @param  array  $fillable
     * @return \Database\ORM\Model
     */
    public function fillable(array $fillable)
    {
        $this->fillable = $fillable;

        return $this;
    }

    /**
     * Set the guarded attributes for the Model.
     *
     * @param  array  $guarded
     * @return \Database\ORM\Model
     */
    public function guard(array $guarded)
    {
        $this->guarded = $guarded;

        return $this;
    }

    /**
     * Disable all mass assignable restrictions.
     *
     * @return void
     */
    public static function unguard()
    {
        static::$unguarded = true;
    }

    /**
     * Enable the mass assignment restrictions.
     *
     * @return void
     */
    public static function reguard()
    {
        static::$unguarded = false;
    }

    /**
     * Set "unguard" to a given state.
     *
     * @param  bool  $state
     * @return void
     */
    public static function setUnguardState($state)
    {
        static::$unguarded = $state;
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
     * Determine if the Model is totally guarded.
     *
     * @return bool
     */
    public function totallyGuarded()
    {
        return ((count($this->fillable) == 0) && ($this->guarded == array('*')));
    }

    /**
     * Get a new Query for the Model's table.
     *
     * @return \Database\ORM\Builder
     */
    public function newQuery()
    {
        $builder = $this->newBuilder($this->newBaseQueryBuilder());

        // Once we have the query builders, we will set the model instances so the
        // builder can easily access any information it may need from the model
        // while it is constructing and executing various queries against it.
        return $builder->setModel($this)->with($this->with);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Database\Query\Builder $query
     * @return \Database\ORM\Builder|static
     */
    public function newBuilder($query)
    {
        return new Builder($query);
    }

    /**
     * Get a new QueryBuilder instance for the Connection.
     *
     * @return \Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new QueryBuilder($connection);
    }

    /**
     * Create a new ORM Collection instance.
     *
     * @param  array  $models
     * @return \Database\ORM\Collection
     */
    public function newCollection(array $models = array())
    {
        return new Collection($models);
    }

    /**
     * Create a new pivot model instance.
     *
     * @param  \Database\ORM\Model  $parent
     * @param  array   $attributes
     * @param  string  $table
     * @param  bool    $exists
     * @return \Database\ORM\Relations\Pivot
     */
    public function newPivot(Model $parent, array $attributes, $table, $exists)
    {
        return new Pivot($parent, $attributes, $table, $exists);
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

    /**
     * Get the database Connection instance.
     *
     * @return \Database\Connection
     */
    public function getConnection()
    {
        return Connection::getInstance($this->connection);
    }

    /**
     * Get the current Connection name for the Model.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connection;
    }

    /**
     * Set the Connection associated with the Model.
     *
     * @param  string  $name
     * @return \Database\ORM\Model
     */
    public function setConnection($name)
    {
        $this->connection = $name;

        return $this;
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
    public function toArray()
    {
        return $this->attributes;
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
        return $this->getAttribute($key);
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
        return (isset($this->attributes[$key]) ||
            ($this->hasGetMutator($key) && ! is_null($this->getAttributeValue($key))));
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
