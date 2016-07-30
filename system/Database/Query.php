<?php

namespace Database;

use Database\Query\Expression;
use Database\Query\Builder as QueryBuilder;

use Closure;


class Query
{
    /**
     * The base Query Builder instance.
     *
     * @var \Database\Query\Builder
     */
    protected $query;

    /**
     * The model being queried.
     *
     * @var \Database\Model
     */
    protected $model;


    /**
     * Create a new Model Query Builder instance.
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
     * @return mixed|static|null
     */
    public function find($id, $columns = array('*'))
    {
        if (is_array($id)) {
            return $this->findMany($id, $columns);
        }

        $query = $this->query->where($this->model->getKeyName(), '=', $id);

        return $query->first($columns);
    }

    /**
     * Find a model by its primary key.
     *
     * @param  array  $ids
     * @param  array  $columns
     * @return array|null|static
     */
    public function findMany($ids, $columns = array('*'))
    {
        if (empty($ids)) return null;

        $query = $this->query->whereIn($this->model->getKeyName(), $ids);

        return $query->get($columns);
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
        $results = $this->get($columns);

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

        $query = $this->query->forPage($page, $perPage);

        // Retrieve the results from database.
        $results = $query->get($columns);

        return $paginator->make($results, $total, $perPage);
    }

    /**
     * Get a Paginator only supporting simple next and previous links.
     *
     * This is more efficient on larger data-sets, etc.
     *
     * @param  int    $perPage
     * @param  array  $columns
     * @return \Pagination\Paginator
     */
    public function simplePaginate($perPage = null, $columns = array('*'))
    {
        // Get the Pagination Factory instance.
        $paginator = $this->connection->getPaginator();

        $perPage = $perPage ?: $this->model->getPerPage();

        $page = $paginator->getCurrentPage();

        $query = $this->skip(($page - 1) * $perPage)->take($perPage + 1);

        // Retrieve the results from database.
        $results = $query->get($columns);

        return $paginator->make($results, $perPage);
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
        $result = call_user_func_array(array($this->query, $method), $parameters);

        if ($result === $this->query) return $this;

        return $result;
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
