<?php
/**
 * Profiler
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Config\Config;


/**
 * Setup the Profiler configuration
 */
Config::set('profiler', array(
    'useForensics' => false,
    'withDatabase' => false,
));
