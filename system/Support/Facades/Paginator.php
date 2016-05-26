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
    public static function getFactory()
    {
        if (! isset(static::$factory)) {
            $request = Request::instance();

            // Setup the Factory instance.
            static::$factory = new Factory($request);
        }

        // Return the Factory instance.
        return static::$factory;
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
