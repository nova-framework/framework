<?php
/**
 * Bootstrap handler - perform the Nova Framework's bootstrap stage.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 10th, 2016
 */

use Core\Aliases;
use Core\Router;
use Helpers\Session;
use App\Config;


/** Turn on output buffering. */
ob_start();

/** Initialize the Configuration */
Config::init();

/** Initialize the Aliases */
Aliases::init();

/** Start the Session. */
Session::init();

/** Initialize the Language. */
Language::init();

/** Load the Framework wide functions. */
require dirname(__FILE__) .DS .'functions.php';

/** Get the Router instance. */
$router = Router::getInstance();

/** Load the Routes */
require APPDIR .'Routes.php';

/** Execute matched Routes. */
$router->dispatch();
