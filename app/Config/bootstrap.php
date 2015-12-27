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

require $configDir .'constants.php';
require $configDir .'functions.php';
require $configDir .'config.php';

/**
 * Turn on custom error handling.
 */

Logger::initialize();


set_exception_handler('Nova\Logger::ExceptionHandler');
set_error_handler('Nova\Logger::ErrorHandler');

/**
 * Set timezone.
 */
date_default_timezone_set(Config::get('timezone'));

/**
 * Start sessions.
 */
Session::initialize();

/** Get the Router instance. */
$router = Router::getInstance();

/** load routes */
require $configDir .'routes.php';

/** bootstrap the active modules (and their associated routes) */
Modules::bootstrap();

/** initialize the Events */
Events::initialize();

/** Execute matched routes. */
$router->dispatch();
