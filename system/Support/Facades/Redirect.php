<?php
/**
 * Request - A Facade to the \Http\Request.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Routing\Redirector;

use ReflectionMethod;
use ReflectionException;


class Redirect
{
    /**
     * Magic Method for calling the methods on a RedirectResponse instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        // Get a RedirectResponse instance.
        $instance = new Redirector();

        // Call the non-static method from the Request instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
