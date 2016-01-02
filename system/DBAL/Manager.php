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


    public static function getLink($linkName = 'default')
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
        if (isset($config['driver'])) {
            $driver = $config['driver'];
        }
        else {
            $driver = 'pdo_mysql';
        }

        // Will set the wrapperClass when provided in the config.
        if (isset($config['wrapperClass'])) {
            $wrapperClass = $config['wrapperClass'];
        }
        else {
            $wrapperClass = '\Nova\DBAL\Connection';
        }

        if(! class_exists($wrapperClass)) {
            throw new \Exception("No valid Wrapper Class is given: " .$wrapperClass);
        }

        //
        $linkParams = array(
            'dbname'       => $options['database'],
            'user'         => $options['username'],
            'password'     => $options['password'],
            'host'         => $options['host'],
            'driver'       => $driver,
            'wrapperClass' => $wrapperClass
        );

        $linkConfig = new Configuration();

        $link = DriverManager::getConnection($linkParams, $linkConfig);

        // Save instance
        static::$instances[$linkName] = $link;

        // Return instance
        return $link;
    }

}
