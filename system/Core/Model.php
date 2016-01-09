<?php
/**
 * Model - the base model
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date June 27, 2014
 * @date updated January 9th 2016
 */

namespace Nova\Core;

use Nova\Database\Connection;
use Nova\Database\Manager as Database;

/**
 * Base model class all other models will extend from this base.
 */
abstract class Model
{
    /**
     * Hold the database connection.
     *
     * @var object
     */
    protected $db;

    /**
     * Setup the instance of Database Connection.
     *
     * @param string $connection Connection name or Database Connection instance, default is string 'default'
     */
    public function __construct($connection = null)
    {
        if($connection instanceof Connection) {
            // Set the given Database Connection.
            $this->db = $connection;
        }
        else {
            $connection = ($connection !== null) ? $connection : 'default';

            // Setup the Database Connection.
            $this->db = Database::getConnection($connection);
        }
    }

}
