<?php
/**
 * Database configuration
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Nova\Config\Config;


/**
 * Setup the Database configuration.
 */
Config::set('database', array(
    // The PDO Fetch Style.
    'fetch' => PDO::FETCH_CLASS,

    // The Default Database Connection Name.
    'default' => 'mysql',

    //--------------------------------------------------------------------------
    //  The Database Connections
    //--------------------------------------------------------------------------

    'connections' => array(
        'sqlite' => array(
            'driver'    => 'sqlite',
            'database'  => APPDIR .'Storage' .DS .'database.sqlite',
            'prefix'    => '',
        ),
        'mysql' => array(
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'nova',
            'username'  => 'nova',
            'password'  => 'password',
            'prefix'    => PREFIX,
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
        ),
        'pgsql' => array(
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'database' => 'nova',
            'username' => 'nova',
            'password' => 'password',
            'charset'  => 'utf8',
            'prefix'   => PREFIX,
            'schema'   => 'public',
        ),
    ),

    //--------------------------------------------------------------------------
    // Migration Repository Table
    //--------------------------------------------------------------------------

    'migrations' => 'migrations',

    //--------------------------------------------------------------------------
    // Redis Databases
    //--------------------------------------------------------------------------

    'redis' => array(
        'cluster' => false,

        'default' => array(
            'host'     => '127.0.0.1',
            'port'     => 6379,
            'database' => 0,
        ),
    ),

    //--------------------------------------------------------------------------
    // Database Backup
    //--------------------------------------------------------------------------

    'backup' => array(
        // The path where database dumps are stored.
        'path'  => APPDIR .'Database' .DS .'Backup',

        // The paths to the MySQL tools used by Forge.
        'mysql' => array(
            'dumpCommandPath'    => '/usr/bin/mysqldump',
            'restoreCommandPath' => '/usr/bin/mysql',
        ),

        // Wheter or not the dump file is compressed.
        'compress' => true,
    ),
));
