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

use Nova\Database\Manager;

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
     *
     * @param string $linkName Custom connection name, default is 'default'
     */
    public function __construct($linkName = 'default')
    {
        /** connect to Database Engine here. */
        $this->db = Manager::getEngine($linkName);
    }

    /**
     * Provide direct access to any of the Database Engine instance methods
     * BUT make it look like it's part of the Class; purely for convenience.
     *
     * @param $name
     * @param $params
     */
    public function __call($method, $params = null)
    {
        if (method_exists($this->db, $name))
        {
            return call_user_func_array([$this->db, $name], $params);
        }

        return false;
    }

}
