<?php
/**
 * Query - A QueryBuilder with support for the ORM Models.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database\ORM;

use Database\Connection;
use Database\Query as BaseQuery;
use Database\ORM\Model;


class Query extends BaseQuery
{
    /**
     * The Model being queried.
     *
     * @var \Database\ORM\Model
     */
    protected $model = null;

    /**
     * Create a new Query instance.
     *
     * @return void
     */
    public function __construct(Model $model)
    {
        // Set the Model being queried.
        $this->model = $model;

        // Get a Connection instance according with that specified on the Model instance.
        $connection = Connection::getInstance($model->getConnection());

        // Finally, execute the Parent Constructor, with Connection instance as parameter.
        parent::construct($connection);
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
     * @return array
     */
    public function get($columns = array('*'))
    {
        $results = parent::get($columns);

        return $this->getModels($results);
    }

    /**
     * Convert results to Model instances.
     *
     * @param  array  $results
     * @return array
     */
    public function getModels(array $results)
    {
        $models = array();

        foreach ($results as $result) {
            $models[] = $this->model->newModel($result);
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

        $results = $this->get($columns);

        $this->columns = null;

        $this->aggregate = null;

        if (count($results) > 0) {
            $result = reset($results);

            // Convert the Model instance to array.
            $result = $result->toArray();

            return $result['aggregate'];
        }
    }

    /**
     * Delete a Record from the database.
     *
     * @param  mixed  $id
     * @return int
     */
    public function delete()
    {
        if (func_num_args() > 0) {
            $keyName = $this->model->getKeyName();

            $id = reset(func_get_args());

            $this->where($keyName, '=', $id);
        }

        return parent::delete();
    }

    /**
     * Set a Model instance for the Model being queried.
     *
     * @param  \Database\ORM\Model|null  $model
     * @return \Database\ORM\Query
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }
}

