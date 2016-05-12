<?php
/**
 * Model - the base model
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */

namespace Core;

use Helpers\Database;

/**
 * Base model class. All other models will extend from this base.
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
     * Create a new instance of the database helper.
     */
    public function __construct()
    {
        // Connect to PDO here.
        $this->db = Database::get();
    }
}
