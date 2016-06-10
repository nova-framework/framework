<?php
/**
 * Database configuration
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Core\Config;


/**
 * Setup the Database configuration.
 */
Config::set('database', array(
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
            'driver'    => DB_TYPE,
            'hostname'  => DB_HOST,
            'database'  => DB_NAME,
            'username'  => DB_USER,
            'password'  => DB_PASS,
            'prefix'    => PREFIX,
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
        ),
        'pgsql' => array(
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'database' => 'database',
            'username' => 'root',
            'password' => '',
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
        ),
    ),
));
