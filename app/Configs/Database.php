<?php
/**
 * Database configuration
 *
 * @author David Carr - dave@daveismyname.com
 * @author Edwin Hoksberg - info@edwinhoksberg.nl
 * @version 3.0
 */

use Core\Config;

//
// database details ONLY NEEDED IF USING A DATABASE

/**
 * Database engine, default is mysql.
 */
define('DB_TYPE', 'mysql');

/**
 * Database host, default is localhost.
 */
define('DB_HOST', 'localhost');

/**
 * Database name.
 */
define('DB_NAME', 'dbname');

/**
 * Database username.
 */
define('DB_USER', 'root');

/**
 * Database password.
 */
define('DB_PASS', 'password');

/**
 * PREFER to be used in database calls, default is nova_
 */
define('PREFIX', 'nova_');

/**
 * Setup the Database configuration.
 */
Config::set('database', array(
    'default' => array(
        'driver'    => DB_TYPE,
        'hostname'  => DB_HOST,
        'database'  => DB_NAME,
        'username'  => DB_USER,
        'password'  => DB_PASS,
        'prefix'    => PREFIX,
        'charset'   => 'utf8',
        'collation' => 'utf8_general_ci',
    ),
));