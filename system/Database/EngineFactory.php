<?php


namespace Smvc\Database;


use Smvc\Database\Engine\Engine;

abstract class EngineFactory
{
    const DRIVER_MYSQL = "MySQLEngine";

    private static $instances = array();

    /**
     * Get instance of the database engine you prefer.
     * Please use the constants in this class as a driver parameter
     *
     * @param $driver string Driver class name (use constants in factory)
     * @param null|array $config Array of configuration
     * @return null|Engine|\PDO
     * @throws \Exception
     */
    public static function getEngine($driver = null, $config = null)
    {
        // If no driver given, use default
        if ($driver === null) {
            $driver = constant("static::DRIVER_" . strtoupper(DB_TYPE));
            if ($driver === null) {
                throw new \Exception("Driver not found, check your config.php, DB_TYPE");
            }
        }

        // If no config is given, use the default
        if ($config === null) {
            $config = array(
                'host' => DB_HOST,
                'database' => DB_NAME,
                'user' => DB_USER,
                'password' => DB_PASS,
                'prefix' => PREFIX
            );
        }

        // Config string
        $configString = $driver . ':' . $config['host'] . ':' . $config['database'] . ':' . $config['user'] . ':'
            . $config['password'] . ':' . $config['prefix'];

        // Engine
        if (isset(static::$instances[$configString])) {
            return static::$instances[$configString];
        }

        // Make new instance, can throw exceptions!
        $class = '\Smvc\Core\Database\Engine\\' . $driver;
        $engine = new $class($config);

        // If no success
        if (!$engine instanceof Engine) {
            return null;
        }

        // Save instance
        static::$instances[$configString] = $engine;

        // Return instance
        return $engine;
    }
}
