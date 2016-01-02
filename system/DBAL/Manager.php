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

        if (!isset($config[$linkName])) {
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
        if (isset($options['wrapperClass'])) {
            $wrapperClass = $options['wrapperClass'];
        }
        else {
            $wrapperClass = '\Nova\DBAL\Connection';
        }

        if(! class_exists($wrapperClass)) {
            throw new \Exception("No valid Wrapper Class is given: " .$wrapperClass);
        }

        $linkParams = $options['config'];

        // Will set the default fetchMode and fetchClass when provided in the config.

        if (isset($linkParams['return_type'])) {
            $returnType = $linkParams['return_type'];
        }
        else {
            $returnType = null;
        }

        //
        $fetchClass = null;

        if($returnType == 'array') {
            $fetchMode = PDO::FETCH_ASSOC;
        }
        else if($returnType == 'object') {
            $fetchMode = PDO::FETCH_OBJ;
        }
        else if($returnType !== null) {
            $classPath = str_replace('\\', '/', ltrim($returnType, '\\'));

            if(! preg_match('#^App(?:/Modules/.+)?/Models/Entities/(.*)$#i', $classPath)) {
                throw new \Exception("No valid Entity Name is given: " .$returnType);
            }

            if(! class_exists($returnType)) {
                throw new \Exception("No valid Entity Class is given: " .$returnType);
            }

            $fetchClass = $returnType;

            $fetchMode = PDO::FETCH_CLASS;
        }
        else {
            // By default we use this FetchMode.
            $fetchMode = PDO::FETCH_ASSOC;
        }

        //
        $linkParams['driver'] = $driver;

        $linkParams['wrapperClass'] = $wrapperClass;

        // Get the Configuration instance
        $linkConfig = new Configuration();

        // Get a Connection instance
        $connection = DriverManager::getConnection($linkParams, $linkConfig);

        // Set the (default) FetchMode and FetchType
        $connection->setFetchMode($fetchMode);
        $connection->setFetchType($returnType);

        // Save instance
        static::$instances[$linkName] = $connection;

        // Return instance
        return $connection;
    }

}
