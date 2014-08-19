<?php

if(file_exists('vendor/autoload.php')){
	require 'vendor/autoload.php';
} else {
	echo "<h1>Please install via composer.json</h1>";
	echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
	echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
	exit;
}

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
	define('ENVIRONMENT', 'development');
/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but production will hide them.
 *
 */

if (defined('ENVIRONMENT')){

	switch (ENVIRONMENT){
		case 'development':
			error_reporting(E_ALL);
		break;
	
		case 'production':
			error_reporting(0);
		break;

		default:
			exit('The application environment is not set correctly.');
	}

}

/*
 *---------------------------------------------------------------
 * ERROR REPORTING > LOG TO FILE
 *---------------------------------------------------------------
 *
 * If you want to report the errors to a log, please define LOG_REPORT to 
 * true and set a file to save the log (LOG_SRC). By default, the errors
 * will be saved in /error.log file.
 */

define('LOG_REPORT', false);
define('LOG_SRC', 'error.log');


if(defined(LOG_REPORT) && LOG_REPORT) {

	error_reporting(E_ALL);
	ini_set("log_errors", 1);
	ini_set("error_log", LOG_SRC);
	
}

//create alias for Router
use \core\router as Router,
    \helpers\url as Url;

//define routes
Router::any('', '\controllers\welcome@index');

//if no route found
Router::error('\core\error@index');

//execute matched routes
Router::dispatch();
