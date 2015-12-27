<?php
/**
 * Bootstrap handler - perform the Nova Framework's bootstrap stage.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 15th, 2015
 */

use Nova\Modules\Manager as Modules;
use Nova\Events\Manager as Events;
use Nova\Net\Session;
use Nova\Net\Router;
use Nova\Config;
use Nova\Logger;

// The current configuration files directory.
$configDir = dirname(__FILE__) .DS;

/**
 * Turn on output buffering.
 */
ob_start();

/**
 * Load the application wide Configuration.
 */
require $configDir .'constants.php';
require $configDir .'functions.php';
require $configDir .'config.php';

/**
 * Turn on custom error handling.
 */
Logger::initialize();

/**
 * Set the Framework Exception and Error Handlers
 */
set_exception_handler('Nova\Logger::ExceptionHandler');
set_error_handler('Nova\Logger::ErrorHandler');

/**
 * Set the Timezone.
 */
date_default_timezone_set(Config::get('timezone'));

/**
 * Start the Sessions.
 */
Session::initialize();

/** Get the Router instance */
$router = Router::getInstance();

/** Bootstrap the active Modules */
Modules::bootstrap();

/** Initialize the Events Management */
Events::initialize();

/** Load the application wide Routes */
require $configDir .'routes.php';

/** Execute the Routes matching. */
$router->dispatch();
