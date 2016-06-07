<?php
/**
 * Database - A Facade to the Database Connection.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Config\LoaderManager;
use Config\Repository;
use Database\Connection;


class Config
{
    /**
     * The Configuration Repository instance being handled.
     *
     * @var \Config\Repository|null
     */
    protected static $repository;


    /**
     * Return the default Repository instance.
     *
     * @return \Config\Repository
     */
    protected static function getRepository()
    {
        if (isset(static::$repository)) {
            return static::$repository;
        }

        // Get a LoaderManager instance
        $loader = new LoaderManager();

        // Get a Database Connection instance and setup it.
        $connection = Connection::getInstance();

        $loader->setConnection($connection);

        return static::$repository = new Config($loader);
    }

    /**
     * Return the default Repository instance.
     *
     * @return \Config\Repository
     */
    public static function instance()
    {
        return static::getRepository();
    }

    /**
     * Magic Method for calling the methods on the default Repository instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = static::getRepository();

        // Call the non-static method from the Connection instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
