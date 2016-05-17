<?php
/**
 * Query - A QueryBuilder with support for the ORM Models.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database\ORM;

use Database\Connection;
use Database\Query\Builder as BaseBuilder;
use Database\ORM\Model;


class Builder extends BaseBuilder
{
    /**
     * The Model being queried.
     *
     * @var \Database\ORM\Model
     */
    protected $model = null;

    /**
     * Create a new Builder instance.
     *
     * @return void
     */
    public function __construct(Model $model)
    {
        // Set the Model being queried.
        $this->model = $model;

        // Get a Connection instance from the Model being queried.
        $connection = $model->getConnection();

        // Finally, execute the parent's Constructor.
        parent::__construct($connection);
    }

    /**
     * Execute a query for a single record by ID.
     *
     * @param  int    $id
     * @param  array  $columns
     * @return mixed
     */
    public function find($id, $columns = array('*'))
    {
        $keyName = $this->model->getKeyName();

        return $this->where($keyName, '=', $id)->first($columns);
    }

    /**
     * Find a Model by its primary key.
     *
     * @param  array  $id
     * @param  array  $columns
     * @return array|static
     */
    public function findMany($ids, $columns = array('*'))
    {
        if (empty($ids)) return array();

        $this->query->whereIn($this->model->getKeyName(), $ids);

        return $this->get($columns);
    }

    /**
     * Pluck a single column's value from the first result of a query.
     *
     * @param  string  $column
     * @return mixed
     */
    public function pluck($column)
    {
        $result = $this->first(array($column));

        if (! is_null($result)) {
            // Convert the Model instance to array.
            $result = $result->toArray();
        }

        return (count($result) > 0) ? reset($result) : null;
    }

    /**
     * Execute the query as a "SELECT" statement.
     *
     * @param  array  $columns
     * @return \Database\ORM\Model|static[]
     */
    public function get($columns = array('*'))
    {
        return $this->getModels($columns);
    }

    /**
     + Get the hydrated Models without eager loading.
     *
     * @param  array  $columns
     * @return \Database\ORM\Model|static[]
     */
    public function getModels($columns = array('*'))
    {
        $results = parent::get($columns);

        $connection = $this->model->getConnectionName();

        // Create an array of Models.
        $models = array();

        foreach ($results as $result) {
            $result = (array) $result;

            $models[] = $model = $this->model->newFromBuilder($result);

            $model->setConnection($connection);
        }

        return $models;
    }

    /**
     * Execute an aggregate function on the database.
     *
     * @param  string  $function
     * @param  array   $columns
     * @return mixed
     */
    public function aggregate($function, $columns = array('*'))
    {
        $this->aggregate = compact('function', 'columns');

        $previousColumns = $this->columns;

        $results = $this->get($columns);

        $this->aggregate = null;

        $this->columns = $previousColumns;

        if (isset($results[0])) {
            $result = $results[0];

            // Convert the Model instance to array.
            $result = $result->toArray();

            $result = (array) $result;

            return $result['aggregate'];
        }
    }

    /**
     * Delete a Record from the database.
     *
     * @return int
     */
    public function delete()
    {
        return parent::delete();
    }

    /**
     * Get the Model instance being queried.
     *
     * @return \Database\ORM\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set a Model instance for the Model being queried.
     *
     * @param  \Database\ORM\Model|null  $model
     * @return \Database\ORM\Builder
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }
}

