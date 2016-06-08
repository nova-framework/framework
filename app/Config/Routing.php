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
        //':hex' => '[[:xdigit:]]+',
    ),
    'default' => array(
        'controller' => DEFAULT_CONTROLLER,
        'method'     => DEFAULT_METHOD
    )
));
