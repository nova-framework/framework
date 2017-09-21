<?php
/**
 * Database configuration
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


return array(
    // The PDO Fetch Style.
    'fetch' => PDO::FETCH_CLASS,

    // The Default Database Connection Name.
    'default' => 'mysql',

    // The Database Connections.
    'connections' => array(
        'sqlite' => array(
            'driver'    => 'sqlite',
            'database'  => APPDIR .'Storage' .DS .'database.sqlite',
            'prefix'    => '',
        ),
        'mysql' => array(
            'driver'    => 'mysql',
            'hostname'  => 'localhost',
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

    // Migration Repository Table
    'migrations' => 'migrations',

    // Database Backup
    'backup' => array(
        // The path where database dumps are stored.
        'path'  => APPDIR .'Database' .DS .'Backup',

        // The paths to the MySQL tools used by Forge.
        'mysql' => array(
            'dumpCommandPath'    => '/usr/bin/mysqldump',
            'restoreCommandPath' => '/usr/bin/mysql',
        ),

        // Whether or not the dump file is compressed.
        'compress' => true,
    ),
);
