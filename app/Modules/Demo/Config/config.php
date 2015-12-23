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
 * Events Management
 */

Events::addListener('welcome', 'App\Modules\Demo\Controllers\EventListener@welcome');
