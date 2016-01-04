<?php
/**
 * Manager - Manage the DBAL's Connections.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 2th, 2016
 */

namespace Nova\DBAL;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;
use Nova\Config;
use PDO;


class Manager
{
    /** @var Connection[] connection instances */
    private static $instances = array();


    public static function getConnection($linkName = 'default')
    {
        $config = Config::get('database');

        if (! isset($config[$linkName])) {
            throw new \Exception("Connection name '".$linkName."' is not defined in your configuration!");
        }

        // Connection, when already have an instance, return it!
        if (isset(static::$instances[$linkName])) {
            return static::$instances[$linkName];
        }

        $options = $config[$linkName];

        // Will set the driver when provided in the config.
        if (isset($options['driver'])) {
            $driver = $options['driver'];
        }
        else {
            $driver = 'pdo_mysql';
        }

        // Will set the wrapperClass when provided in the config.
        if (isset($options['wrapper_class'])) {
            $wrapperClass = $options['wrapper_class'];
        }
        else {
            $wrapperClass = '\Nova\DBAL\Connection';
        }

        if(! class_exists($wrapperClass)) {
            throw new \Exception("No valid Wrapper Class is given: " .$wrapperClass);
        }

        // Will set the default fetchMode and fetchType when provided in the config.

        if (isset($options['return_type'])) {
            $fetchType = $options['return_type'];
        }
        else {
            $fetchType = 'array';
        }

        // Prepare the FetchMode and check the FetchType
        if($fetchType == 'array') {
            $fetchMode = PDO::FETCH_ASSOC;
        }
        else if($fetchType == 'object') {
            $fetchMode = PDO::FETCH_OBJ;
        }
        else {
            $fetchMode = PDO::FETCH_CLASS;

            // Check for a valid Entity on given className.
            $className = $fetchType;

            $classPath = str_replace('\\', '/', ltrim($className, '\\'));

            if(! preg_match('#^App(?:/Modules/.+)?/Models/Entities/(.*)$#i', $classPath)) {
                throw new \Exception("No valid Entity Name is given: " .$className);
            }

            if(! class_exists($className)) {
                throw new \Exception("No valid Entity Class is given: " .$className);
            }
        }

        $linkParams = $options['config'];

        //
        $linkParams['driver'] = $driver;

        $linkParams['wrapperClass'] = $wrapperClass;

        // Get the Configuration instance
        $linkConfig = new Configuration();

        // Get a Connection instance
        $connection = DriverManager::getConnection($linkParams, $linkConfig);

        // Set the (default) FetchMode and FetchType
        $connection->setFetchMode($fetchMode);
        $connection->setFetchType($fetchType);

        // Save instance
        static::$instances[$linkName] = $connection;

        // Return instance
        return $connection;
    }

}
