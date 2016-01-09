<?php
/**
 * Manager - Manage the DBAL's Connections.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 2th, 2016
 */

namespace App\Packages\DBAL;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;

use App\Packages\DBAL\Connection;
use Nova\Config;

use PDO;


class Manager
{
    /** @var Connection[] connection instances */
    private static $instances = array();


    /**
     * Get connection instance
     *
     * @param string $linkName Optional custom link name
     * @return Connection Connection instance
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public static function getConnection($linkName = 'default')
    {
        $config = Config::get('database');

        if (! isset($config[$linkName])) {
            throw new \Exception(__d('dbal', 'Connection name \'{0}\' is not defined in your configuration!', $linkName));
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
            $wrapperClass = '\App\Packages\DBAL\Connection';
        }

        if(! class_exists($wrapperClass)) {
            throw new \Exception(__d('dbal', 'No valid Wrapper Class is given: {0}', $wrapperClass));
        }

        // Will set the default fetchMode and fetchType when provided in the config.

        if (isset($options['return_type'])) {
            $fetchType = $options['return_type'];
        }
        else {
            $fetchType = 'array';
        }

        // Prepare the FetchMode and check the FetchType
        $fetchMode = Connection::getFetchMode($fetchType);

        // Prepare the Connection parameters.
        $linkParams = $options['config'];

        $linkParams['driver'] = $driver;

        $linkParams['wrapperClass'] = $wrapperClass;

        // Get the Configuration instance
        $linkConfig = new Configuration();

        // Get a Connection instance
        /** @var Connection $connection */
        $connection = DriverManager::getConnection($linkParams, $linkConfig);

        // Set the (default) FetchMode and FetchType
        $connection->setFetchMode($fetchMode);
        $connection->setFetchType($fetchType);

        // Save instance
        static::$instances[$linkName] = $connection;

        // Return instance
        return $connection;
    }

    public static function clearConnections()
    {
        foreach(static::$instances as $name => $connection) {
            $connection->close();
        }

        static::$instances = array();
    }

}
