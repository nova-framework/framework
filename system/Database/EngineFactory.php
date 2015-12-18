<?php


namespace Smvc\Database;


use Smvc\Config;
use Smvc\Database\Engine\Engine;

abstract class EngineFactory
{
    const DRIVER_MYSQL = "MySQLEngine";
    const DRIVER_SQLITE = "SQLiteEngine";

    private static $instances = array();

    /**
     * Get instance of the database engine you prefer.
     * Please use the constants in this class as a driver parameter
     *
     * @param $connectionName string Name of the connection provided in the configuration
     * @return null|Engine|\PDO
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
        if (isset(static::$instances[$connectionName])) {
            return static::$instances[$connectionName];
        }

        // Make new instance, can throw exceptions!
        $class = '\Smvc\Database\Engine\\' . $driver;
        $engine = new $class($engineConfig['config']);

        // If no success
        if (!$engine instanceof Engine) {
            throw new \Exception("Driver creation failed! Check your extended logs for errors.");
        }

        // Save instance
        static::$instances[$connectionName] = $engine;

        // Return instance
        return $engine;
    }
}
