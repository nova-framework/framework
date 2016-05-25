<?php
/**
 * Database configuration
 *
 * @author David Carr - dave@daveismyname.com
 * @author Edwin Hoksberg - info@edwinhoksberg.nl
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Core\Config;

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