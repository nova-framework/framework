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
    'parameters' => 'named' // The style of parameters processed on Route pattern; supported: 'named' and 'unnamed'.
));
