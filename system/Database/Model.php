<?php
/**
 * Model - A simple Database Model.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database;

use Database\Connection;
use Helpers\Inflector;


class Model
{
    /**
     * The database connection instance.
     *
     * @var \Database\Connection
     */
    protected $db;

    /**
     * The table associated with the Model.
     *
     * @var string
     */
    protected $table = null;

    /**
     * The primary key for the Model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Create a new Model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct()
    {
        if(is_null($this->table)) {
            // If there is not a Table name specified, try to auto-calculate it.
            $className = get_class($this);

            if($className != 'Database\Model') {
                // A child Class with no Table specified; try to auto-configure.
                $this->table = Inflector::tableize(class_basename($className));
            }
        }

        $this->db = Connection::getInstance();
    }

    /**
     * Get the Table for the Model.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Get the Primary Key for the Model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * Get a new Query for the Model's table.
     *
     * @return \Database\Query
     */
    public function newQuery()
    {
        return $this->db->table($this->table);
    }

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $query = $this->newQuery();

        return call_user_func_array(array($query, $method), $parameters);
    }
}
