<?php
/**
 * Bootstrap handler - perform the Nova Framework's bootstrap stage.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 10th, 2016
 */

use Core\Aliases;
use Core\Language;
use Core\Logger;
use Core\Modules;
use Routing\Router;
use Support\Facades\Session;

use Patchwork\Utf8\Bootup as Patchwork;


/** Turn on the custom error handling. */
set_exception_handler('Core\Logger::ExceptionHandler');
set_error_handler('Core\Logger::ErrorHandler');

/** Turn on output buffering. */
ob_start();

/** Load the Configuration. */
require APPDIR .'Config.php';

/** Set the Default Timezone. */
date_default_timezone_set(DEFAULT_TIMEZONE);

/** Load the Framework wide functions. */
require dirname(__FILE__) .DS .'functions.php';

/** Initialize the Class Aliases. */
Aliases::init();

/** Initialize the Logger. */
Logger::init();

/** Start the Session. */
Session::init();

/** Initialize the Language. */
Language::init();

/** Initialize the Patchwork Utf8. */
Patchwork::initAll();

/** Load the Events. */
require APPDIR .'Events.php';

/** Load the Route Filters */
require APPDIR .'Filters.php';

/** Initialize the active Modules. */
Modules::init();

/** Get the Router instance. */
$router = Router::getInstance();

/** Load the Routes */
require APPDIR .'Routes.php';

/** Load the Routes from the active Modules */
Modules::loadRoutes();

/** Execute matched Routes. */
$router->dispatch();
