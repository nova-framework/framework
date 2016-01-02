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


class Manager
{
    /** @var Connection[] connection instances */
    private static $nstances = array();


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

        //
        $linkParams['driver'] = $driver;

        $linkParams['wrapperClass'] = $wrapperClass;

        // Get the Configuration instance
        $linkConfig = new Configuration();

        $connection = DriverManager::getConnection($linkParams, $linkConfig);

        // Save instance
        static::$instances[$linkName] = $connection;

        // Return instance
        return $connection;
    }

}
