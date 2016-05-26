<?php
/**
 * Paginator - A Facade to the Pagination Factory.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Pagination\Factory;
use Support\Facades\Request;


class Paginator
{
    /**
     * The Factory instance being handled.
     *
     * @var \Pagination\Factory|null
     */
    protected static $factory;

    /**
     * Return a Pagination Factory instance
     *
     * @return \Pagination\Factory
     */
    protected static function getFactory()
    {
        if (isset(static::$factory)) {
            return static::$factory;
        }

        // Get the Request instance.
        $request = Request::instance();

        // Setup and return the Pagination Factory instance.
        return static::$factory = new Factory($request);
    }

    /**
     * Return the default Pagination Factory instance.
     *
     * @return \Encryption\Encrypter
     */
    public static function instance()
    {
        return static::getFactory();
    }

    /**
     * Magic Method for calling the methods on the default Pagination Factory instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = static::getFactory();

        // Call the non-static method from the Pagination Factory instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
