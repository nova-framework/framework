<?php
/**
 * Facade - A Facade to the Events Dispatcher.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Events\Dispatcher;


class Event
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
