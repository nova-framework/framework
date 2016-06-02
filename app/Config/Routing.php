<?php
/**
 * Routing Configuration
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Core\Config;


/**
 * Routing configuration
 */
Config::set('routing', array(
    'patterns' => array(
        //':hex'    => '[[:xdigit:]]+',
        //':uuidV4' => '\w{8}-\w{4}-\w{4}-\w{4}-\w{12}'
    ),
    'dispatcher' => array(
        'defaultController' => DEFAULT_CONTROLLER,
        'defaultMethod'     => DEFAULT_METHOD
    )
));
