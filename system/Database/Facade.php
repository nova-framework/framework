<?php
/**
 * Facade - A static Facade to Database Connection.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database;

use Database\Connection;


class Facade
{
    /**
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = Connection::getInstance();

        // Call the non-static method from the Class instance
        return call_user_func_array(array($instance, $method), $args);
    }
}
