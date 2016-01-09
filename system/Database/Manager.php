<?php
/**
 * Engine Manager (Factory).
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 19th, 2015
 */

namespace Nova\Database;


use Nova\Config;
use Nova\Database\Connection;
use Nova\Core\Controller;
use Nova\Helpers\Inflector;


abstract class Manager
{
    const DRIVER_MYSQL  = "MySQL";
    const DRIVER_SQLITE = "SQLite";

    /** @var Connection[] Connection instances */
    private static $instances = array();


    /**
     * Get instance of the database engine you prefer.
     * Please use the constants in this class as a driver parameter
     *
     * @param $linkName string Name of the connection provided in the configuration
     * @return Connection|\PDO|null
     * @throws \Exception
     */
    public static function getConnection($linkName = 'default')
    {
        $config = Config::get('database');

        if (!isset($config[$linkName])) {
            throw new \Exception(__d('system', 'Connection name \'{0}\' is not defined in your configuration!', $linkName));
        }

        $options = $config[$linkName];

        // Make the engine
        $driverName = strtoupper($options['driver']);

        if(strpos($driverName, 'PDO_') === 0) {
            $driver = constant("static::DRIVER_" .str_replace('PDO_', '', $driverName));
        }
        else {
            throw new \Exception(__d('system', 'Driver not found, check your config.php'));
        }

        // Engine, when already have an instance, return it!
        if (isset(static::$instances[$linkName])) {
            return static::$instances[$linkName];
        }

        // Make new instance, can throw exceptions!
        $className = '\Nova\Database\Driver\\' . $driver;

        if (! class_exists($className)) {
            throw new \Exception(__d('system', 'Class not found: {0}', $className));
        }

        $connection = new $className($options['config']);

        // If no success
        if (! $connection instanceof Connection) {
            throw new \Exception(__d('system', 'Driver creation failed! Check your extended logs for errors.'));
        }

        // Save instance
        static::$instances[$linkName] = $connection;

        // Return instance
        return $connection;
    }

}
