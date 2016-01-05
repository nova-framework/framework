<?php
/**
 * Engine Manager (Factory).
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date December 19th, 2015
 */

namespace Nova\Database;


use Nova\Config;
use Nova\Database\Engine;
use Nova\Core\Controller;
use Nova\Helpers\Inflector;


abstract class Manager
{
    const DRIVER_MYSQL = "MySQL";
    const DRIVER_SQLITE = "SQLite";

    /** @var Engine[] engine instances */
    private static $engineInstances = array();

    /** @var Service[] service instances */
    private static $serviceInstances = array();

    /**
     * Get instance of the database engine you prefer.
     * Please use the constants in this class as a driver parameter
     *
     * @param $linkName string Name of the connection provided in the configuration
     * @return Engine|\PDO|null
     * @throws \Exception
     */
    public static function getEngine($linkName = 'default')
    {
        $config = Config::get('database');

        if (!isset($config[$linkName])) {
            throw new \Exception("Connection name '".$linkName."' is not defined in your configuration!");
        }

        $options = $config[$linkName];

        // Make the engine
        $engineName = $options['engine'];

        $driver = constant("static::DRIVER_" . strtoupper($engineName));

        if ($driver === null) {
            throw new \Exception("Driver not found, check your config.php, DB_TYPE");
        }

        // Engine, when already have an instance, return it!
        if (isset(static::$engineInstances[$linkName])) {
            return static::$engineInstances[$linkName];
        }

        // Make new instance, can throw exceptions!
        $className = '\Nova\Database\Engine\\' . $driver;

        if (! class_exists($className)) {
            throw new \Exception("Class not found: ".$className);
        }

        $engine = new $className($options['config']);

        // If no success
        if (! $engine instanceof Engine) {
            throw new \Exception("Driver creation failed! Check your extended logs for errors.");
        }

        // Save instance
        static::$engineInstances[$linkName] = $engine;

        // Return instance
        return $engine;
    }

}
