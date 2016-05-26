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

use Database\ORM\Relations\Pivot;
use Database\ORM\Relations\Relation;

use Database\ORM\Relations\BelongsTo;
use Database\ORM\Relations\BelongsToMany;
use Database\ORM\Relations\HasOne;
use Database\ORM\Relations\HasMany;
use Database\ORM\Relations\HasManyThrough;

use Database\ORM\Relations\MorphTo;
use Database\ORM\Relations\MorphToMany;
use Database\ORM\Relations\MorphOne;
use Database\ORM\Relations\MorphMany;

use Support\Contracts\ArrayableInterface;
use Support\Contracts\JsonableInterface;

use Carbon\Carbon;

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
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = array();

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = array();

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
     protected $appends = array();

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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = array();

    /**
     * The relationships that should be touched on save.
     *
     * @var array
     */
    protected $touches = array();

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = array();

    /**
     * The class name to be used in polymorphic relations.
     *
     * @var string
     */
    protected $morphClass;

    /**
     * Indicates if the Model exists.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * Indicates whether attributes are snake cased on arrays.
     *
     * @var bool
     */
    public static $snakeAttributes = true;

    /**
     * The array of booted models.
     *
     * @var array
     */
    protected static $booted = array();

    /**
     * Indicates if all mass assignment is enabled.
     *
     * @var bool
     */
    protected static $unguarded = true;

    /**
     * The cache of the mutated attributes for each class.
     *
     * @var array
     */
    protected static $mutatorCache = array();

    /**
     * The many to many relationship methods.
     *
     * @var array
     */
    public static $manyMethods = array('belongsToMany', 'morphToMany', 'morphedByMany');

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

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

        $this->bootIfNotBooted();

        $this->syncOriginal();

        $this->fill($attributes);
    }

    /**
     * Check if the Model needs to be booted and if so, do it.
     *
     * @return void
     */
    protected function bootIfNotBooted()
    {
        if (! isset(static::$booted[get_class($this)])) {
            static::$booted[get_class($this)] = true;

            static::boot();
        }
    }

    /**
     * The "booting" method of the Model.
     *
     * @return void
     */
    protected static function boot()
    {
        $class = get_called_class();

        static::$mutatorCache[$class] = array();

        // Here we will extract all of the mutated attributes so that we can quickly
        // spin through them after we export models to their array form, which we
        // need to be fast. This will let us always know the attributes mutate.
        foreach (get_class_methods($class) as $method) {
            if (preg_match('/^get(.+)Attribute$/', $method, $matches)) {
                if (static::$snakeAttributes) {
                    $matches[1] = Inflector::tableize($matches[1]);
                }

                static::$mutatorCache[$class][] = lcfirst($matches[1]);
            }
        }
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
     * Create a collection of models from plain arrays.
     *
     * @param  array  $items
     * @param  string  $connection
     * @return \Database\ORM\Collection
     */
    public static function hydrate(array $items, $connection = null)
    {
        $collection = with($instance = new static)->newCollection();

        foreach ($items as $item) {
            $model = $instance->newFromBuilder($item);

            if (! is_null($connection)) {
                $model->setConnection($connection);
            }

            $collection->push($model);
        }

        return $collection;
    }

    /**
     * Create a collection of Models from a raw query.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  string  $connection
     * @return \Database\ORM\Collection
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
     * @return \Database\ORM\Collection|static[]
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
     * @return \Database\ORM\Model|Collection|static
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
     * @return \Database\ORM\Model|Collection|static
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
     * @return \Database\ORM\Model|Collection|static
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
     * Define a polymorphic one-to-one relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Database\ORM\Relations\MorphOne
     */
    public function morphOne($related, $name, $type = null, $id = null, $localKey = null)
    {
        $instance = new $related;

        list($type, $id) = $this->getMorphs($name, $type, $id);

        $table = $instance->getTable();

        $localKey = $localKey ?: $this->getKeyName();

        return new MorphOne($instance->newQuery(), $this, $table .'.'.$type, $table .'.' .$id, $localKey);
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
            $foreignKey = Inflector::tableize($relation).'_id';
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
     * Define a polymorphic, inverse one-to-one or many relationship.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @return \Database\ORM\Relations\MorphTo
     */
    public function morphTo($name = null, $type = null, $id = null)
    {
        // If no name is provided, we will use the backtrace to get the function name
        // since that is most likely the name of the polymorphic interface. We can
        // use that to get both the class and foreign key that will be utilized.
        if (is_null($name)) {
            list(, $caller) = debug_backtrace(false);

            $name = Inflector::tableize($caller['function']);
        }

        list($type, $id) = $this->getMorphs($name, $type, $id);

        // If the type value is null it is probably safe to assume we're eager loading
        // the relationship. When that is the case we will pass in a dummy query as
        // there are multiple types in the morph and we can't use single queries.
        if (is_null($class = $this->$type)) {
            return new MorphTo($this->newQuery(), $this, $id, null, $type, $name);
        }

        // If we are not eager loading the relationship we will essentially treat this
        // as a belongs-to style relationship since morph-to extends that class and
        // we will pass in the appropriate values so that it behaves as expected.
        else {
            $instance = new $class;

            return new MorphTo(with($instance)->newQuery(), $this, $id, $instance->getKeyName(), $type, $name);
        }
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
     * Define a polymorphic one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Database\ORM\Relations\MorphMany
     */
    public function morphMany($related, $name, $type = null, $id = null, $localKey = null)
    {
        $instance = new $related;

        // Here we will gather up the morph type and ID for the relationship so that we
        // can properly query the intermediate table of a relation. Finally, we will
        // get the table and create the relationship instances for the developers.
        list($type, $id) = $this->getMorphs($name, $type, $id);

        $table = $instance->getTable();

        $localKey = $localKey ?: $this->getKeyName();

        return new MorphMany($instance->newQuery(), $this, $table .'.' .$type, $table .'.' .$id, $localKey);
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
     * Define a polymorphic many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $table
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @param  bool    $inverse
     * @return \Database\ORM\Relations\MorphToMany
     */
    public function morphToMany($related, $name, $table = null, $foreignKey = null, $otherKey = null, $inverse = false)
    {
        $caller = $this->getBelongsToManyCaller();

        // First, we will need to determine the foreign key and "other key" for the
        // relationship. Once we have determined the keys we will make the query
        // instances, as well as the relationship instances we need for these.
        $foreignKey = $foreignKey ?: $name.'_id';

        $instance = new $related;

        $otherKey = $otherKey ?: $instance->getForeignKey();

        // Now we're ready to create a new query builder for this related model and
        // the relationship instances for this relation. This relations will set
        // appropriate query constraints then entirely manages the hydrations.
        $query = $instance->newQuery();

        $table = $table ?: Inflector::pluralize($name);

        return new MorphToMany($query, $this, $name, $table, $foreignKey, $otherKey, $caller, $inverse);
    }

    /**
     * Define a polymorphic, inverse many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $table
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @return \Database\ORM\Relations\MorphToMany
     */
    public function morphedByMany($related, $name, $table = null, $foreignKey = null, $otherKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        // For the inverse of the polymorphic many-to-many relations, we will change
        // the way we determine the foreign and other keys, as it is the opposite
        // of the morph-to-many method since we're figuring out these inverses.
        $otherKey = $otherKey ?: $name.'_id';

        return $this->morphToMany($related, $name, $table, $foreignKey, $otherKey, true);
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

            return (! in_array($caller, Model::$manyMethods) && $caller != $self);
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
            // Here, we'll touch the owning models, verifying these timestamps get updated
            // for the models. This will allow any caching to get broken on the parents
            // by the timestamp. Then we will go ahead and delete the model instance.
            $this->touchOwners();

            //
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

            if (array_get($options, 'touch', true)) $this->touchOwners();
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
            // First we need to create a fresh query instance and touch the creation and
            // update timestamp on the model which are maintained by us for developer
            // convenience. Then we will just continue saving the model instances.
            if ($this->timestamps) {
                $this->updateTimestamps();
            }

            $dirty = $this->getDirty();

            if (count($dirty) > 0) {
                $this->setKeysForSaveQuery($query)->update($dirty);
            }
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
        if ($this->timestamps) {
            $this->updateTimestamps();
        }

        $attributes = $this->attributes;

        if ($this->incrementing) {
            $this->insertAndSetId($query, $attributes);
        } else {
            $query->insert($attributes);
        }

        $this->exists = true;

        return true;
    }

    /**
     * Insert the given attributes and set the ID on the Model.
     *
     * @param  \Database\ORM\Builder  $query
     * @param  array  $attributes
     * @return void
     */
    protected function insertAndSetId(Builder $query, $attributes)
    {
        $id = $query->insertGetId($attributes, $keyName = $this->getKeyName());

        $this->setAttribute($keyName, $id);
    }

    /**
     * Touch the owning relations of the Model.
     *
     * @return void
     */
    public function touchOwners()
    {
        foreach ($this->touches as $relation) {
            $this->$relation()->touch();
        }
    }

    /**
     * Determine if the model touches a given relation.
     *
     * @param  string  $relation
     * @return bool
     */
    public function touches($relation)
    {
        return in_array($relation, $this->touches);
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
     * Update the model's update timestamp.
     *
     * @return bool
     */
    public function touch()
    {
        $this->updateTimestamps();

        return $this->save();
    }

    /**
     * Update the creation and update timestamps.
     *
     * @return void
     */
    protected function updateTimestamps()
    {
        $time = $this->freshTimestamp();

        if ( ! $this->isDirty(static::UPDATED_AT)) {
            $this->setUpdatedAt($time);
        }

        if ( ! $this->exists && ! $this->isDirty(static::CREATED_AT)) {
            $this->setCreatedAt($time);
        }
    }

    /**
     * Set the value of the "created at" attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setCreatedAt($value)
    {
        $this->{static::CREATED_AT} = $value;
    }

    /**
     * Set the value of the "updated at" attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setUpdatedAt($value)
    {
        $this->{static::UPDATED_AT} = $value;
    }

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function getCreatedAtColumn()
    {
        return static::CREATED_AT;
    }

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function getUpdatedAtColumn()
    {
        return static::UPDATED_AT;
    }

    /**
     * Get a fresh timestamp for the model.
     *
     * @return \Carbon\Carbon
     */
    public function freshTimestamp()
    {
        return new Carbon();
    }

    /**
     * Get a fresh timestamp for the model.
     *
     * @return string
     */
    public function freshTimestampString()
    {
        return $this->fromDateTime($this->freshTimestamp());
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

    public function getTable()
    {
       if (isset($this->table)) return $this->table;

       $baseName = Inflector::pluralize(class_basename($this));

       return str_replace('\\', '', Inflector::tableize($baseName));
    }

    /**
     * Set the table associated with the model.
     *
     * @param  string  $table
     * @return void
     */
    public function setTable($table)
    {
        $this->table = $table;
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
     * Determine if the model uses timestamps.
     *
     * @return bool
     */
    public function usesTimestamps()
    {
        return $this->timestamps;
    }

    /**
     * Get the polymorphic relationship columns.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @return array
     */
    protected function getMorphs($name, $type, $id)
    {
        $type = $type ?: $name.'_type';

        $id = $id ?: $name.'_id';

        return array($type, $id);
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return $this->morphClass ?: get_class($this);
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
     * Get the hidden attributes for the model.
     *
     * @return array
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Set the hidden attributes for the model.
     *
     * @param  array  $hidden
     * @return void
     */
    public function setHidden(array $hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * Set the visible attributes for the model.
     *
     * @param  array  $visible
     * @return void
     */
    public function setVisible(array $visible)
    {
        $this->visible = $visible;
    }

    /**
     * Set the accessors to append to model arrays.
     *
     * @param  array  $appends
     * @return void
     */
    public function setAppends(array $appends)
    {
        $this->appends = $appends;
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
        if (static::$unguarded)  return true;

        if (in_array($key, $this->fillable)) return true;

        if ($this->isGuarded($key)) return false;

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
     * Remove the table name from a given key.
     *
     * @param  string  $key
     * @return string
     */
    protected function removeTableFromKey($key)
    {
        if (! str_contains($key, '.')) return $key;

        return last(explode('.', $key));
    }

    /**
     * Get the relationships that are touched on save.
     *
     * @return array
     */
    public function getTouchedRelations()
    {
        return $this->touches;
    }

    /**
     * Set the relationships that are touched on save.
     *
     * @param  array  $touches
     * @return void
     */
    public function setTouchedRelations(array $touches)
    {
        $this->touches = $touches;
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return $this->incrementing;
    }

    /**
     * Set whether IDs are incrementing.
     *
     * @param  bool  $value
     * @return void
     */
    public function setIncrementing($value)
    {
        $this->incrementing = $value;
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
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $attributes = $this->attributesToArray();

        return array_merge($attributes, $this->relationsToArray());
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = $this->getArrayableAttributes();

        // If an attribute is a date, we will cast it to a string after converting it
        // to a DateTime / Carbon instance. This is so we will get some consistent
        // formatting while accessing attributes vs. arraying / JSONing a model.
        foreach ($this->getDates() as $key) {
            if (! isset($attributes[$key])) continue;

            $attributes[$key] = (string) $this->asDateTime($attributes[$key]);
        }

        // We want to spin through all the mutated attributes for this model and call
        // the mutator for the attribute. We cache off every mutated attributes so
        // we don't have to constantly check on attributes that actually change.
        foreach ($this->getMutatedAttributes() as $key) {
            if (! array_key_exists($key, $attributes)) continue;

            $attributes[$key] = $this->mutateAttributeForArray(
                $key, $attributes[$key]
            );
        }

        // Here we will grab all of the appended, calculated attributes to this model
        // as these attributes are not really in the attributes array, but are run
        // when we need to array or JSON the model for convenience to the coder.
        foreach ($this->appends as $key) {
            $attributes[$key] = $this->mutateAttributeForArray($key, null);
        }

        return $attributes;
    }

    /**
     * Get an attribute array of all arrayable attributes.
     *
     * @return array
     */
    protected function getArrayableAttributes()
    {
        return $this->getArrayableItems($this->attributes);
    }

    /**
     * Get the model's relationships in array form.
     *
     * @return array
     */
    public function relationsToArray()
    {
        $attributes = array();

        foreach ($this->getArrayableRelations() as $key => $value) {
            if (in_array($key, $this->hidden)) continue;

            // If the values implements the Arrayable interface we can just call this
            // toArray method on the instances which will convert both models and
            // collections to their proper array form and we'll set the values.
            if ($value instanceof ArrayableInterface) {
                $relation = $value->toArray();
            }

            // If the value is null, we'll still go ahead and set it in this list of
            // attributes since null is used to represent empty relationships if
            // if it a has one or belongs to type relationships on the models.
            else if (is_null($value)) {
                $relation = $value;
            }

            // If the relationships snake-casing is enabled, we will snake case this
            // key so that the relation attribute is snake cased in this returned
            // array to the developers, making this consistent with attributes.
            if (static::$snakeAttributes) {
                $key = snake_case($key);
            }

            // If the relation value has been set, we will set it on this attributes
            // list for returning. If it was not arrayable or null, we'll not set
            // the value on the array because it is some type of invalid value.
            if (isset($relation) || is_null($value)) {
                $attributes[$key] = $relation;
            }
        }

        return $attributes;
    }

    /**
     * Get an attribute array of all arrayable relations.
     *
     * @return array
     */
    protected function getArrayableRelations()
    {
        return $this->getArrayableItems($this->relations);
    }

    /**
     * Get an attribute array of all arrayable values.
     *
     * @param  array  $values
     * @return array
     */
    protected function getArrayableItems(array $values)
    {
        if (count($this->visible) > 0) {
            return array_intersect_key($values, array_flip($this->visible));
        }

        return array_diff_key($values, array_flip($this->hidden));
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

        if (array_key_exists($key, $this->relations)) {
            return $this->relations[$key];
        }

        // If the "attribute" exists as a method on the model, we will just assume
        // it is a relationship and will load and return results from the query
        // and hydrate the relationship's value on the "relationships" array.
        $camelKey = Inflector::camelize($key);

        if (method_exists($this, $camelKey)) {
            return $this->getRelationshipFromMethod($key, $camelKey);
        }
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
        } else if (in_array($key, $this->getDates())) {
            if ($value) return $this->asDateTime($value);
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
     * Get a relationship value from a method.
     *
     * @param  string  $key
     * @param  string  $camelKey
     * @return mixed
     *
     * @throws \LogicException
     */
    protected function getRelationshipFromMethod($key, $camelKey)
    {
        $relations = $this->$camelKey();

        if (! $relations instanceof Relation) {
            throw new LogicException('Relationship method must return an object of type Database\ORM\Relations\Relation');
        }

        return $this->relations[$key] = $relations->getResults();
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
     * Get the attributes that should be converted to dates.
     *
     * @return array
     */
    public function getDates()
    {
        $defaults = array(static::CREATED_AT, static::UPDATED_AT);

        return array_merge($this->dates, $defaults);
    }

    /**
     * Convert a DateTime to a storable string.
     *
     * @param  \DateTime|int  $value
     * @return string
     */
    public function fromDateTime($value)
    {
        $format = $this->getDateFormat();

        // If the value is already a DateTime instance, we will just skip the rest of
        // these checks since they will be a waste of time, and hinder performance
        // when checking the field. We will just return the DateTime right away.
        if ($value instanceof DateTime) {
            //
        }

        // If the value is totally numeric, we will assume it is a UNIX timestamp and
        // format the date as such. Once we have the date in DateTime form we will
        // format it according to the proper format for the database connection.
        else if (is_numeric($value)) {
            $value = Carbon::createFromTimestamp($value);
        }

        // If the value is in simple year, month, day format, we will format it using
        // that setup. This is for simple "date" fields which do not have hours on
        // the field. This conveniently picks up those dates and format correct.
        elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value)) {
            $value = Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        }

        // If this value is some other type of string, we'll create the DateTime with
        // the format used by the database connection. Once we get the instance we
        // can return back the finally formatted DateTime instances to the devs.
        elseif (! $value instanceof DateTime) {
            $value = Carbon::createFromFormat($format, $value);
        }

        return $value->format($format);
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value
     * @return \Carbon\Carbon
     */
    protected function asDateTime($value)
    {
        // If this value is an integer, we will assume it is a UNIX timestamp's value
        // and format a Carbon object from this timestamp. This allows flexibility
        // when defining your date fields as they might be UNIX timestamps here.
        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value);
        }

        // If the value is in simply year, month, day format, we will instantiate the
        // Carbon instances from that format. Again, this provides for simple date
        // fields on the database, while still supporting Carbonized conversion.
        else if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value)) {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        }

        // Finally, we will just assume this date is in the format used by default on
        // the database connection and use that format to create the Carbon object
        // that is returned back out to the developers after we convert it here.
        else if ( ! $value instanceof DateTime) {
            $format = $this->getDateFormat();

            return Carbon::createFromFormat($format, $value);
        }

        return Carbon::instance($value);
    }

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    protected function getDateFormat()
    {
        return $this->getConnection()->getDateFormat();
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
     * Get the value of an attribute using its mutator for array conversion.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return mixed
     */
    protected function mutateAttributeForArray($key, $value)
    {
        $value = $this->mutateAttribute($key, $value);

        return ($value instanceof ArrayableInterface) ? $value->toArray() : $value;
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

            return call_user_func(array($this, $method), $value);
        } else if (in_array($key, $this->getDates())) {
            if ($value) {
                $value = $this->fromDateTime($value);
            }
        }

        $this->attributes[$key] = $value;
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
     * Clone the model into a new, non-existing instance.
     *
     * @return \Database\ORM\Model
     */
    public function replicate()
    {
        $attributes = array_except($this->attributes, array($this->getKeyName()));

        with($instance = new static)->setRawAttributes($attributes);

        return $instance->setRelations($this->relations);
    }

    /**
     * Get all of the current attributes on the model.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
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

        if ($sync) $this->syncOriginal();
    }

    /**
     * Get the model's original attribute values.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return array
     */
    public function getOriginal($key = null, $default = null)
    {
        return array_get($this->original, $key, $default);
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
     * Determine if a given attribute is dirty.
     *
     * @param  string  $attribute
     * @return bool
     */
    public function isDirty($attribute)
    {
        return array_key_exists($attribute, $this->getDirty());
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
            if ( ! array_key_exists($key, $this->original)) {
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

        return (is_numeric($current) && is_numeric($original) && (strcmp((string) $current, (string) $original) === 0));
    }

    /**
     * Get all the loaded relations for the instance.
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * Get a specified relationship.
     *
     * @param  string  $relation
     * @return mixed
     */
    public function getRelation($relation)
    {
        return $this->relations[$relation];
    }

    /**
     * Set the specific relationship in the model.
     *
     * @param  string  $relation
     * @param  mixed   $value
     * @return Database\ORM\Model
     */
    public function setRelation($relation, $value)
    {
        $this->relations[$relation] = $value;

        return $this;
    }

    /**
     * Set the entire relations array on the model.
     *
     * @param  array  $relations
     * @return Database\ORM\Model
     */
    public function setRelations(array $relations)
    {
        $this->relations = $relations;

        return $this;
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
     * Get the mutated attributes for a given instance.
     *
     * @return array
     */
    public function getMutatedAttributes()
    {
        $class = get_class($this);

        if (isset(static::$mutatorCache[$class])) {
            return static::$mutatorCache[get_class($this)];
        }

        return array();
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
        return (isset($this->attributes[$key]) || isset($this->relations[$key]) ||
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

        unset($this->relations[$key]);
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
        if (in_array($method, array('increment', 'decrement'))) {
            return call_user_func_array(array($this, $method), $parameters);
        }

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

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

}
