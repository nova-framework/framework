<?php
// IF NOT RUNNING PHPUNIT, EXIT DIRECTLY!!
if (! defined('PHPUNIT_RUNNING')) { exit(); }

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

// Require composer autoload!
require_once dirname(__FILE__) . DS . 'vendor' . DS . 'autoload.php';

/** Define the absolute paths for configured directories (changed for tests) */
define('BASEPATH', realpath(__DIR__).DS);
// The Application paths.
define('WEBPATH', BASEPATH.'public'.DS);
define('APPPATH', BASEPATH.'app'.DS);
define('SYSPATH', BASEPATH.'system'.DS);


use Nova\Modules\Manager as Modules;
use Nova\Events\Manager as Events;
use Nova\Net\Router;
use Nova\Config;


/**
 * CUSTOMIZED CONFIG BOOTSTRAP
 */
$configDir = dirname(__FILE__) . DS . 'app' . DS . 'Config' . DS;
require_once $configDir.'constants.php';
require_once $configDir.'functions.php';
require_once $configDir.'config.php';


/**
 * Set timezone.
 */
date_default_timezone_set(Config::get('timezone'));

/**
 * Start sessions.
 */
//Session::init(); // No session in tests!

/**
 * Get the Router instance.
 * */
$router = Router::getInstance();

/**
 * load routes
 * */
require_once $configDir.'routes.php'; // Default routes, we will only change the routes later in tests!

/**
 * bootstrap the active modules (and their associated routes)
 * */
Modules::bootstrap();

/**
 * initialize the Events
 * */
Events::initialize();

// Let the errors be reported so we could see them in our consoles!
error_reporting(E_ALL);
set_exception_handler(null);
set_error_handler(null);
