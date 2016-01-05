<?php
/**
 * Model - the base model
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Nova\Core;

use Nova\DBAL\Connection;
use Nova\DBAL\Manager as Database;

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
     * Setup the instance of DBAL Connection.
     *
     * @param string $connection Connection name or DBAL Connection instance, default is string 'default'
     */
    public function __construct($connection = null)
    {
        if($connection instanceof Connection) {
            // Set the given Database Connection.
            $this->db = $connection;
        }
        else {
            $connection = ($connection !== null) ? $connection : 'default';

            // Setup the DBAL Connection.
            $this->db = Database::getConnection($connection);
        }
    }

}
