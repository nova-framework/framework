<?php
/**
 * Request - A Facade to the \Http\Request.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Http\Request as HttpRequest;

use ReflectionMethod;
use ReflectionException;


class Request
{
    /**
     * The \Http\Request instance being handled.
     *
     * @var \Validation\Factory|null
     */
    protected static $request;

    /**
     * Return a \Http\Request instance
     *
     * @return \Http\Request
     */
    protected static function getRequest()
    {
        if (isset(static::$request)) {
            return static::$request;
        }

        return static::$request = HttpRequest::createFromGlobals();
    }

    /**
     * Magic Method for calling the methods on the default Request instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        // First handle the static Methods from HttpRequest.
        try {
            $reflection = new ReflectionMethod(HttpRequest::class, $method);

            if ($reflection->isStatic()) {
                // The Method is static.
                return call_user_func_array(array(HttpRequest::class, $method), $params);
            }
        } catch ( ReflectionException $e ) {
            // Nothing to do.
        }

        // Get a HttpRequest instance.
        $instance = static::getRequest();

        // Support for checking the HTTP Method via isX.
        if (str_starts_with($method, 'is') && (strlen($method) > 4)) {
            return ($instance->method() == strtoupper(substr($method, 2)));
        }

        // Call the non-static method from the Request instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
