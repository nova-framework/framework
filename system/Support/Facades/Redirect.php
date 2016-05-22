<?php
/**
 * Redirect - A Facade to the \Routing\Redirector.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Routing\Redirector;
use Support\Facades\Request;


class Redirect
{
    /**
     * The Session Store instance being handled.
     *
     * @var \Routing\Redirector|null
     */
    protected static $redirector;

    /**
     * Return a Session Store instance
     *
     * @return \Routing\Redirector
     */
    protected static function getRedirector()
    {
        if (isset(static::$redirector)) {
            return static::$redirector;
        }

        // Get the Request instance.
        $request = Request::instance();

        return static::$sessionStore = new Redirector($request);
    }

    /**
     * Magic Method for calling the methods on a Redirector instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        // Get a Redirector instance.
        $instance = static::getRedirector();

        // Call the non-static method from the Redirector instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
