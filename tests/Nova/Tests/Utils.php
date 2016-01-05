<?php
/**
 * Test Utils
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date January 5th, 2016
 */

namespace Nova\Tests;

use Nova\Config;
use Nova\DBAL\Manager;

class Utils
{

    /**
     * Configurations for db's
     * @var array
     */
    private static $dbConfig = array(
        'mysql' => array(
            'engine' => 'mysql',
            'driver' => 'pdo_mysql',
            'config' => array(
                'host'        => 'localhost',
                'port'        => 3306,     // Not required, default is 3306
                'dbname'      => 'testdb1',
                'user'        => 'root',
                'password'    => '',
                'charset'     => 'utf8',   // Not required, default and recommended is utf8.
                'return_type' => 'object'  // Not required, default is 'array'.
            )
        ),
        'sqlite' => array(
            'engine' => 'sqlite',
            'driver'  => 'pdo_sqlite',
            'config' => array(
                'path'        => BASEPATH .'storage'.DS.'persistent'.DS.'test.sqlite',
                'return_type' => 'object' // Not required, default is 'array'.
            )
        )
    );


    /**
     * Switch Databases to the right target
     *
     * @param string $db Either mysql or sqlite.
     */
    public static function switchDatabase($db)
    {
        // First clear the current active connections, and clear the array of instances
        Manager::clearConnections();

        // Inject new configuration to the default link
        if (isset(self::$dbConfig[$db])){
            Config::set('database', array(
                'default' => self::$dbConfig[$db]
            ));
        }
    }

}