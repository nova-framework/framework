<?php

namespace Support\Facades;

use Http\Request as HttpRequest;
use Support\Facades\Facade;

use ReflectionMethod;
use ReflectionException;


/**
 * @see \Http\Request
 */
class Request extends Facade
{
    /**
     * Return the Application instance.
     *
     * @return \Http\Request
     */
    public static function instance()
    {
        $accessor = static::getFacadeAccessor();

        return static::resolveFacadeInstance($accessor);
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
                // The requested Method is static.
                return call_user_func_array(array(HttpRequest::class, $method), $params);
            }
        } catch (ReflectionException $e) {
            // Method not found; do nothing.
        }

        // Get a HttpRequest instance.
        $instance = static::instance();

        // Method not found; still support the checking of HTTP Method via isX.
        if (str_starts_with($method, 'is') && (strlen($method) > 4)) {
            return ($instance->method() == strtoupper(substr($method, 2)));
        }

        // Call the non-static method from the Request instance.
        return call_user_func_array(array($instance, $method), $params);
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'request'; }

}
