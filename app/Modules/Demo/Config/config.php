<?php
/**
 * Module configuration - the configuration parameters of the Framework's Module.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 23th, 2015
 */

use Nova\Config;
use Nova\Events\Manager as Events;


/**
 * Additional Configuration for this Module.
 */
Config::set('demo_message', 'Shiny Demo!');


/**
 * Events Management for this Module.
 */

Events::addListener('welcome', 'App\Modules\Demo\Controllers\EventListener@welcome');
