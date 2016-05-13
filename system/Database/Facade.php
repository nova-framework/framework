<?php
/**
 * Facade - A static Facade to the Database Connection.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database;

use Database\Connection;


class Facade
{
    /**
     * Magic Method for calling the methods on the default Connection instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = Connection::getInstance();

        // Call the non-static method from the Connection instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
