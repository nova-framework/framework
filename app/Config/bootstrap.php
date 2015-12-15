<?php

use Smvc\Helpers\Session;

/**
 * Turn on output buffering.
 */
ob_start();

require dirname(__FILE__).'/constants.php';
require dirname(__FILE__).'/config.php';

/**
 * Turn on custom error handling.
 */

set_exception_handler('Smvc\Core\Logger::ExceptionHandler');
set_error_handler('Smvc\Core\Logger::ErrorHandler');

/**
 * Set timezone.
 */
date_default_timezone_set('Europe/Rome');

/**
 * Start sessions.
 */
Session::init();

/** load routes */
require dirname(__FILE__).'/routes.php';
