<?php
/**
 * Bootstrap handler - perform the Nova Framework's bootstrap stage.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 10th, 2016
 */

use Core\Router;

/** initiate Alias */
new \Core\Alias();

/** initiate config */
new \App\Config();

/** Get the Router instance. */
$router = Router::getInstance();

/** load routes */
require APPDIR.'Routes.php';

/** Execute matched routes. */
$router->dispatch();
