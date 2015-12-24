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
     * @param $connectionName string Name of the connection provided in the configuration
     * @return Engine|\PDO|null
     * @throws \Exception
     */
    public static function getEngine($connectionName = 'default')
    {
        $config = Config::get('database');
        if (!isset($config[$connectionName])) {
            throw new \Exception("Connection name '".$connectionName."' is not defined in your configuration!");
        }

        $engineConfig = $config[$connectionName];

        // Make the engine
        $engineName = $engineConfig['engine'];
        $driver = constant("static::DRIVER_" . strtoupper($engineName));
        if ($driver === null) {
            throw new \Exception("Driver not found, check your config.php, DB_TYPE");
        }

        // Engine, when already have an instance, return it!
        if (isset(static::$engineInstances[$connectionName])) {
            return static::$engineInstances[$connectionName];
        }

        // Make new instance, can throw exceptions!
        $class = '\Nova\Database\Engine\\' . $driver;
        $engine = new $class($engineConfig['config']);

        // If no success
        if (!$engine instanceof Engine) {
            throw new \Exception("Driver creation failed! Check your extended logs for errors.");
        }

        // Save instance
        static::$engineInstances[$connectionName] = $engine;

        // Return instance
        return $engine;
    }


    /**
     * Get service instance with class service name
     * @param string $serviceName the relative namespace class name (relative from App\Services\Database\)
     * @param Engine|string|null $engine Use the following engine.
     * @return Service|null
     * @throws \Exception
     */
    public static function getService($serviceName, $engine = 'default')
    {
        $class = 'App\Services\Database\\' . $serviceName;

        if ($engine !== null && is_string($engine)) {
            $engine = self::getEngine($engine);
        }

        if (isset(static::$serviceInstances[$serviceName])) {
            static::$serviceInstances[$serviceName]->setEngine($engine);
            return static::$serviceInstances[$serviceName];
        }

        $service = new $class();

        if (!$service instanceof Service) {
            throw new \Exception("Class not found '".$class."'!");
        }

        $service->setEngine($engine);

        static::$serviceInstances[$serviceName] = $service;

        return $service;
    }
}
