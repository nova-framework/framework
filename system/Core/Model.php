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
    public function __construct($engine = 'default')
    {
        /** connect to Database Engine here. */
        $this->db = Manager::getEngine($engine);
    }

}
