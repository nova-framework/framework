<?php
/**
 * Model - the base model
 *
 * @author David Carr - dave@daveismyname.com
 * @version 3.0
 * @date June 27, 2014
 * @date updated March 9th, 2016
 */

namespace Core;

use Helpers\Database;

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
     * Create a new instance of the database helper.
     */
    public function __construct()
    {
        /** connect to PDO here. */
        $this->db = Database::get();
    }
}
