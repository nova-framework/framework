<?php
namespace Core;

use Helpers\Database;

/*
 * model - the base model
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */

abstract class Model
{
    /**
     * hold the database connection
     * @var object
     */
    protected $db;

    /**
     * create a new instance of the database helper
     */
    public function __construct()
    {
        //connect to PDO here.
        $this->db = Database::get();

    }
}
