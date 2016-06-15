<?php

namespace Database\ORM;

use Database\Query\Expression;
use Database\ORM\Relations\Relation;
use Database\Query\Builder as QueryBuilder;
use Support\Facades\Paginator;

use Closure;


class Builder
{
    /**
     * The base query builder instance.
     *
     * @var \Database\Query\Builder
     */
    protected $query;

    /**
     * The model being queried.
     *
     * @var \Database\ORM\Model
     */
    protected $model;

    /**
     * The relationships that should be eager loaded.
     *
     * @var array
     */
    protected $eagerLoad = array();

    /**
     * The methods that should be returned from query builder.
     *
     * @var array
     */
    protected $passthru = array(
        'toSql', 'lists', 'insert', 'insertGetId', 'pluck', 'count',
        'min', 'max', 'avg', 'sum', 'exists', 'getBindings',
    );

    /**
     * Create a new Eloquent query builder instance.
     *
     * @param  \Database\Query\Builder  $query
     * @return void
     */
    public function __construct(QueryBuilder $query)
    {
        $this->query = $query;
    }

    /**
     * Find a model by its primary key.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return \Database\ORM\Model|static|null
     */
    public function find($id, $columns = array('*'))
    {
        if (is_array($id)) {
            return $this->findMany($id, $columns);
        }

        $this->query->where($this->model->getKeyName(), '=', $id);

        return $this->first($columns);
    }

    /**
     * Find a model by its primary key.
     *
     * @param  array  $id
     * @param  array  $columns
     * @return \Database\ORM\Model|Collection|static
     */
    public function findMany($id, $columns = array('*'))
    {
        if (empty($id)) return $this->model->newCollection();

        $this->query->whereIn($this->model->getKeyName(), $id);

        return $this->get($columns);
    }

    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return \Database\ORM\Model|static
     *
     * @throws ModelNotFoundException
     */
    public function findOrFail($id, $columns = array('*'))
    {
        if (! is_null($model = $this->find($id, $columns))) return $model;

        throw with(new ModelNotFoundException)->setModel(get_class($this->model));
    }

    /**
     * Execute the query and get the first result.
     *
     * @param  array  $columns
     * @return \Database\ORM\Model|static|null
     */
    public function first($columns = array('*'))
    {
        return $this->take(1)->get($columns)->first();
    }

    /**
     * Execute the query and get the first result or throw an exception.
     *
     * @param  array  $columns
     * @return \Database\ORM\Model|static
     *
     * @throws ModelNotFoundException
     */
    public function firstOrFail($columns = array('*'))
    {
        if (! is_null($model = $this->first($columns))) return $model;

        throw with(new ModelNotFoundException)->setModel(get_class($this->model));
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return \Database\ORM\Collection|static[]
     */
    public function get($columns = array('*'))
    {
        $models = $this->getModels($columns);

        if (count($models) > 0) {
            $models = $this->eagerLoadRelations($models);
        }

        return $this->model->newCollection($models);
    }

    /**
     * Pluck a single column from the database.
     *
     * @param  string  $column
     * @return mixed
     */
    public function pluck($column)
    {
        $result = $this->first(array($column));

        if ($result) return $result->{$column};
    }

    /**
     * Chunk the results of the query.
     *
     * @param  int  $count
     * @param  callable  $callback
     * @return void
     */
    public function chunk($count, $callback)
    {
        $results = $this->forPage($page = 1, $count)->get();

        while (count($results) > 0) {
            call_user_func($callback, $results);

            $page++;

            $results = $this->forPage($page, $count)->get();
        }
    }

    /**
     * Get an array with the values of a given column.
     *
     * @param  string  $column
     * @param  string  $key
     * @return array
     */
    public function lists($column, $key = null)
    {
        $results = $this->query->lists($column, $key);

        if ($this->model->hasGetMutator($column)) {
            foreach ($results as $key => &$value) {
                $fill = array($column => $value);

                $value = $this->model->newFromBuilder($fill)->$column;
            }
        }

        return $results;
    }

    /**
     * Get a paginator for the "select" statement.
     *
     * @param  int    $perPage
     * @param  array  $columns
     * @return \Pagination\Paginator
     */
    public function paginate($perPage = null, $columns = array('*'))
    {
        // Get the Pagination Factory instance.
        $paginator = $this->query->getConnection()->getPaginator();

        $perPage = $perPage ?: $this->model->getPerPage();

        if (isset($this->query->groups)) {
            return $this->groupedPaginate($paginator, $perPage, $columns);
        } else {
            return $this->ungroupedPaginate($paginator, $perPage, $columns);
        }
    }

    /**
     * Get a paginator for a grouped statement.
     *
     * @param  \Pagination\Environment  $paginator
     * @param  int    $perPage
     * @param  array  $columns
     * @return \Pagination\Paginator
     */
    protected function groupedPaginate($paginator, $perPage, $columns)
    {
        $results = $this->get($columns)->all();

        return $this->query->buildRawPaginator($paginator, $results, $perPage);
    }

    /**
     * Get a paginator for an ungrouped statement.
     *
     * @param  \Pagination\Environment  $paginator
     * @param  int    $perPage
     * @param  array  $columns
     * @return \Pagination\Paginator
     */
    protected function ungroupedPaginate($paginator, $perPage, $columns)
    {
        $total = $this->query->getPaginationCount();

        $page = $paginator->getCurrentPage($total);

        $this->query->forPage($page, $perPage);

        return $paginator->make($this->get($columns)->all(), $total, $perPage);
    }

    /**
     * Update a record in the database.
     *
     * @param  array  $values
     * @return int
     */
    public function update(array $values)
    {
        return $this->query->update($this->addUpdatedAtColumn($values));
    }

    /**
     * Increment a column's value by a given amount.
     *
     * @param  string  $column
     * @param  int     $amount
     * @param  array   $extra
     * @return int
     */
    public function increment($column, $amount = 1, array $extra = array())
    {
        $extra = $this->addUpdatedAtColumn($extra);

        return $this->query->increment($column, $amount, $extra);
    }

    /**
     * Decrement a column's value by a given amount.
     *
     * @param  string  $column
     * @param  int     $amount
     * @param  array   $extra
     * @return int
     */
    public function decrement($column, $amount = 1, array $extra = array())
    {
        $extra = $this->addUpdatedAtColumn($extra);

        return $this->query->decrement($column, $amount, $extra);
    }

    /**
     * Add the "updated at" column to an array of values.
     *
     * @param  array  $values
     * @return array
     */
    protected function addUpdatedAtColumn(array $values)
    {
        if (! $this->model->usesTimestamps()) return $values;

        $column = $this->model->getUpdatedAtColumn();

        return array_add($values, $column, $this->model->freshTimestampString());
    }

    /**
     * Delete a record from the database.
     *
     * @return int
     */
    public function delete()
    {
        if ($this->model->isSoftDeleting()) {
            return $this->softDelete();
        } else {
            return $this->query->delete();
        }
    }

    /**
     * Soft delete the record in the database.
     *
     * @return int
     */
    protected function softDelete()
    {
        $column = $this->model->getDeletedAtColumn();

        return $this->update(array($column => $this->model->freshTimestampString()));
    }

    /**
     * Force a delete on a set of soft deleted models.
     *
     * @return int
     */
    public function forceDelete()
    {
        return $this->query->delete();
    }

    /**
     * Restore the soft-deleted model instances.
     *
     * @return int
     */
    public function restore()
    {
        if ($this->model->isSoftDeleting()) {
            $column = $this->model->getDeletedAtColumn();

            return $this->update(array($column => null));
        }
    }

    /**
     * Include the soft deleted models in the results.
     *
     * @return \Database\ORM\Builder|static
     */
    public function withTrashed()
    {
        $column = $this->model->getQualifiedDeletedAtColumn();

        foreach ((array) $this->query->wheres as $key => $where) {
            if ($this->isSoftDeleteConstraint($where, $column)) {
                unset($this->query->wheres[$key]);

                $this->query->wheres = array_values($this->query->wheres);
            }
        }

        return $this;
    }

    /**
     * Force the result set to only included soft deletes.
     *
     * @return \Database\ORM\Builder|static
     */
    public function onlyTrashed()
    {
        $this->withTrashed();

        $this->query->whereNotNull($this->model->getQualifiedDeletedAtColumn());

        return $this;
    }

    /**
     * Determine if the given where clause is a soft delete constraint.
     *
     * @param  array   $where
     * @param  string  $column
     * @return bool
     */
    protected function isSoftDeleteConstraint(array $where, $column)
    {
        return $where['type'] == 'Null' && $where['column'] == $column;
    }

    /**
     * Get the hydrated models without eager loading.
     *
     * @param  array  $columns
     * @return array|static[]
     */
    public function getModels($columns = array('*'))
    {
        $results = $this->query->get($columns);

        $connection = $this->model->getConnectionName();

        //
        $models = array();

        foreach ($results as $result) {
            $models[] = $model = $this->model->newFromBuilder($result);

            $model->setConnection($connection);
        }

        return $models;
    }

    /**
     * Eager load the relationships for the models.
     *
     * @param  array  $models
     * @return array
     */
    public function eagerLoadRelations(array $models)
    {
        foreach ($this->eagerLoad as $name => $constraints) {
            if (strpos($name, '.') === false) {
                $models = $this->loadRelation($models, $name, $constraints);
            }
        }

        return $models;
    }

    /**
     * Eagerly load the relationship on a set of models.
     *
     * @param  array     $models
     * @param  string    $name
     * @param  \Closure  $constraints
     * @return array
     */
    protected function loadRelation(array $models, $name, Closure $constraints)
    {
        $relation = $this->getRelation($name);

        $relation->addEagerConstraints($models);

        call_user_func($constraints, $relation);

        $models = $relation->initRelation($models, $name);

        $results = $relation->getEager();

        return $relation->match($models, $results, $name);
    }

    /**
     * Get the relation instance for the given relation name.
     *
     * @param  string  $relation
     * @return \Database\ORM\Relations\Relation
     */
    public function getRelation($relation)
    {
        $me = $this;

        $query = Relation::noConstraints(function() use ($me, $relation)
        {
            return $me->getModel()->$relation();
        });

        $nested = $this->nestedRelations($relation);

        if (count($nested) > 0) {
            $query->getQuery()->with($nested);
        }

        return $query;
    }

    /**
     * Get the deeply nested relations for a given top-level relation.
     *
     * @param  string  $relation
     * @return array
     */
    protected function nestedRelations($relation)
    {
        $nested = array();

        foreach ($this->eagerLoad as $name => $constraints) {
            if ($this->isNested($name, $relation)) {
                $nested[substr($name, strlen($relation .'.'))] = $constraints;
            }
        }

        return $nested;
    }

    /**
     * Determine if the relationship is nested.
     *
     * @param  string  $name
     * @param  string  $relation
     * @return bool
     */
    protected function isNested($name, $relation)
    {
        $dots = str_contains($name, '.');

        return $dots && str_starts_with($name, $relation .'.');
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed   $value
     * @param  string  $boolean
     * @return \Database\ORM\Builder|static
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($column instanceof Closure) {
            $query = $this->model->newQuery(false);

            call_user_func($column, $query);

            $this->query->addNestedWhereQuery($query->getQuery(), $boolean);
        } else {
            call_user_func_array(array($this->query, 'where'), func_get_args());
        }

        return $this;
    }

    /**
     * Add an "or where" clause to the query.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed   $value
     * @return \Database\ORM\Builder|static
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * Add a relationship count condition to the query.
     *
     * @param  string  $relation
     * @param  string  $operator
     * @param  int     $count
     * @param  string  $boolean
     * @param  \Closure  $callback
     * @return \Database\ORM\Builder|static
     */
    public function has($relation, $operator = '>=', $count = 1, $boolean = 'and', $callback = null)
    {
        $relation = $this->getHasRelationQuery($relation);

        $query = $relation->getRelationCountQuery($relation->getRelated()->newQuery(), $this);

        if ($callback) call_user_func($callback, $query);

        return $this->addHasWhere($query, $relation, $operator, $count, $boolean);
    }

    /**
     * Add a relationship count condition to the query with where clauses.
     *
     * @param  string  $relation
     * @param  \Closure  $callback
     * @param  string  $operator
     * @param  int     $count
     * @return \Database\ORM\Builder|static
     */
    public function whereHas($relation, Closure $callback, $operator = '>=', $count = 1)
    {
        return $this->has($relation, $operator, $count, 'and', $callback);
    }

    /**
     * Add a relationship count condition to the query with an "or".
     *
     * @param  string  $relation
     * @param  string  $operator
     * @param  int     $count
     * @return \Database\ORM\Builder|static
     */
    public function orHas($relation, $operator = '>=', $count = 1)
    {
        return $this->has($relation, $operator, $count, 'or');
    }

    /**
     * Add a relationship count condition to the query with where clauses and an "or".
     *
     * @param  string  $relation
     * @param  \Closure  $callback
     * @param  string  $operator
     * @param  int     $count
     * @return \Database\ORM\Builder|static
     */
    public function orWhereHas($relation, Closure $callback, $operator = '>=', $count = 1)
    {
        return $this->has($relation, $operator, $count, 'or', $callback);
    }

    /**
     * Add the "has" condition where clause to the query.
     *
     * @param  \Database\ORM\Builder  $hasQuery
     * @param  \Database\ORM\Relations\Relation  $relation
     * @param  string  $operator
     * @param  int  $count
     * @param  string  $boolean
     * @return \Database\ORM\Builder
     */
    protected function addHasWhere(Builder $hasQuery, Relation $relation, $operator, $count, $boolean)
    {
        $this->mergeWheresToHas($hasQuery, $relation);

        if (is_numeric($count)) {
            $count = new Expression($count);
        }

        return $this->where(new Expression('('.$hasQuery->toSql().')'), $operator, $count, $boolean);
    }

    /**
     * Merge the "wheres" from a relation query to a has query.
     *
     * @param  \Database\ORM\Builder  $hasQuery
     * @param  \Database\ORM\Relations\Relation  $relation
     * @return void
     */
    protected function mergeWheresToHas(Builder $hasQuery, Relation $relation)
    {
        $relationQuery = $relation->getBaseQuery();

        $hasQuery->mergeWheres(
            $relationQuery->wheres, $relationQuery->getBindings()
        );

        $this->query->mergeBindings($hasQuery->getQuery());
    }

    /**
     * Get the "has relation" base query instance.
     *
     * @param  string  $relation
     * @return \Database\ORM\Builder
     */
    protected function getHasRelationQuery($relation)
    {
        $me = $this;

        return Relation::noConstraints(function() use ($me, $relation)
        {
            return $me->getModel()->$relation();
        });
    }

    /**
     * Set the relationships that should be eager loaded.
     *
     * @param  dynamic  $relations
     * @return \Database\ORM\Builder|static
     */
    public function with($relations)
    {
        if (is_string($relations)) $relations = func_get_args();

        $eagers = $this->parseRelations($relations);

        $this->eagerLoad = array_merge($this->eagerLoad, $eagers);

        return $this;
    }

    /**
     * Parse a list of relations into individuals.
     *
     * @param  array  $relations
     * @return array
     */
    protected function parseRelations(array $relations)
    {
        $results = array();

        foreach ($relations as $name => $constraints) {
            if (is_numeric($name)) {
                $f = function() {};

                list($name, $constraints) = array($constraints, $f);
            }

            $results = $this->parseNested($name, $results);

            $results[$name] = $constraints;
        }

        return $results;
    }

    /**
     * Parse the nested relationships in a relation.
     *
     * @param  string  $name
     * @param  array   $results
     * @return array
     */
    protected function parseNested($name, $results)
    {
        $progress = array();

        foreach (explode('.', $name) as $segment) {
            $progress[] = $segment;

            if ( ! isset($results[$last = implode('.', $progress)])) {
                 $results[$last] = function() {};
             }
        }

        return $results;
    }

    /**
     * Call the given model scope on the underlying model.
     *
     * @param  string  $scope
     * @param  array  $parameters
     * @return \Database\Query\Builder
     */
    protected function callScope($scope, $parameters)
    {
        array_unshift($parameters, $this);

        return call_user_func_array(array($this->model, $scope), $parameters) ?: $this;
    }

    /**
     * Get the underlying query builder instance.
     *
     * @return \Database\Query\Builder|static
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the underlying query builder instance.
     *
     * @param  \Database\Query\Builder  $query
     * @return void
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * Get the relationships being eagerly loaded.
     *
     * @return array
     */
    public function getEagerLoads()
    {
        return $this->eagerLoad;
    }

    /**
     * Set the relationships being eagerly loaded.
     *
     * @param  array  $eagerLoad
     * @return void
     */
    public function setEagerLoads(array $eagerLoad)
    {
        $this->eagerLoad = $eagerLoad;
    }

    /**
     * Get the model instance being queried.
     *
     * @return \Database\ORM\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set a model instance for the model being queried.
     *
     * @param  \Database\ORM\Model  $model
     * @return \Database\ORM\Builder
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        $this->query->from($model->getTable());

        return $this;
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this->model, $scope = 'scope' .ucfirst($method))) {
            return $this->callScope($scope, $parameters);
        } else {
            $result = call_user_func_array(array($this->query, $method), $parameters);
        }

        return in_array($method, $this->passthru) ? $result : $this;
    }

    /**
     * Force a clone of the underlying query builder when cloning.
     *
     * @return void
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }

}
