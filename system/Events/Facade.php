<?php
/**
 * Facade - A Facade to Events Dispatcher.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Events;

use Events\Dispatcher;


class Facade
{
    /**
     * Magic Method for calling the methods on the default Dispatcher instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = Dispatcher::getInstance();

        // Call the non-static method from the Dispatcher instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
