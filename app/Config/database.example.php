<?php
/**
 * Database configuration - the configuration parameters of the Framework DBAL.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 15th, 2015
 */

use Nova\Config;


/**
 * Database configurations
 *
 * By default, the 'default' connection will be used when no connection name is given to the engine factory.
 */
Config::set('database', array(
    'default' => array(
        'engine' => 'mysql',
        'driver'  => 'pdo_mysql',
        'config' => array(
            'host'        => 'localhost',
            'port'        => 3306,        // Not required, default is 3306
            'dbname'      => 'dbname',
            'user'        => 'root',
            'password'    => 'password',
            'charset'     => 'utf8',      // Not required, default and recommended is utf8.
            'return_type' => 'assoc',     // Not required, default is 'assoc'.
            'compress'    => false        // Changing to true will hugely improve the persormance on remote servers.
        )
    ),
    /** Extra connections can be added here, some examples: */
    'sqlite' => array(
        'engine' => 'sqlite',
        'driver'  => 'pdo_sqlite',
        'config' => array(
            'path'         => BASEPATH .'storage/persistent/database.sqlite',
            'return_type'  => 'object' // Not required, default is 'assoc'.
        )
    )

));
