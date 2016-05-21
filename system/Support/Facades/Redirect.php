<?php
/**
 * Request - A Facade to the \Http\Request.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Http\RedirectResponse;

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
        // First handle the static Methods from Http\Request.
        try {
            $reflection = new ReflectionMethod(RedirectResponse::class, $method);

            if ($reflection->isStatic()) {
                // The Method is static.
                return call_user_func_array(array(RedirectResponse::class, $method), $params);
            }
        } catch ( ReflectionException $e ) {
            // Nothing to do.
        }

        // Get a RedirectResponse instance.
        $instance = new RedirectResponse();

        // Call the non-static method from the Request instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
