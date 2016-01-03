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


/** Prepare the current directory for configuration files. */
$configDir = APPPATH .'Config' .DS;

/** Check for valid configuration files. */
if (! is_readable($configDir .'config.php') || ! is_readable($configDir .'constants.php')) {
    die('No config.php or constants.php found, configure and rename *.example.php in ' .$configDir);
}

/** Turn on output buffering. */
ob_start();

/** Load the application Constants. */
require $configDir .'constants.php';

/** Load the System's helper functions. */
require dirname(__FILE__) .DS .'functions.php';

/** Load the application Configuration. */
require $configDir .'config.php';

/** Set the current Timezone. */
date_default_timezone_set(Config::get('timezone'));

/** Initialize the Logger. */
Logger::initialize();

/** Set the Framework Exception and Error Handlers. */
set_exception_handler('Nova\Logger::ExceptionHandler');
set_error_handler('Nova\Logger::ErrorHandler');

/** Get the curent Router instance. */
$router = Router::getInstance();

/** Bootstrap the active Modules. */
Modules::bootstrap();

/** Initialize the Events Management. */
Events::initialize();

/** Initialize the Sessions. */
Session::initialize();

/** Load the application wide Routes. */
require $configDir .'routes.php';

/** Execute the Request dispatching by Router. */
$router->dispatch();
