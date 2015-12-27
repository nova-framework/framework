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


    /**
     * Get service instance with class service name
     * @param string $serviceName the relative or absolute namespace class name (relative from App\Services\Database\)
     * @param string|null $fromModule Ask the service to be loaded from specified Module.
     * @param Engine|string|null $engine Use the following engine.
     * @return Service|null
     * @throws \Exception
     */
    public static function getService($serviceName, $fromModule = null, $engine = 'default')
    {
        $serviceName = ltrim($serviceName, '\\');

        // Get the Controller instance.
        $instance =& get_instance();

        // Get the current Module name.
        if(($fromModule !== null) && ! empty($fromModule)) {
            $module = Inflector::classify($fromModule);
        }
        else {
            $module = $instance ? $instance->module() : null;
        }

        //
        // Calculate the Service's fully qualified Class Name.

        $classPath = str_replace('\\', '/', $serviceName);

        if (preg_match('#^App/(Services|Modules)/(.*)$#i', $classPath)) {
            if ($fromModule !== null) {
                throw new \UnexpectedValueException('Fully qualified Class Name while a Module is specified');
            }

            // A fully qualified className.
            $basePath = '\\';
        }
        else if($module !== null) {
            $basePath = '\App\Modules\\'.$module.'\Services\Database\\';
        }
        else {
            $basePath = '\App\Services\Database\\';
        }

        $className = $basePath.$serviceName;

        if (($engine !== null) && is_string($engine)) {
            $engine = self::getEngine($engine);
        }

        if (isset(static::$serviceInstances[$className])) {
            $service =& static::$serviceInstances[$className];

            $service->engine($engine);

            return $service;
        }

        if (! class_exists($className)) {
            throw new \Exception("Class not found: ".$className);
        }

        // Get a Service instance.
        $service = new $className();

        if (! $service instanceof Service) {
            throw new \Exception("Invalid Service called: ".$className);
        }

        $service->engine($engine);

        static::$serviceInstances[$className] = $service;

        return $service;
    }

}
