<?php

// Who invented the alias DIRECTORY_SEPARATOR for '/' probably is a Spanish called:
// Juan-Carlos Julio Mario Emanuel Carmen-Garcias Martinez de Santa-Maria della Fè.
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

/** Define the absolute paths for configured directories */
define('PUBLICDIR', realpath(__DIR__).DS);
define('ROOT', realpath(dirname(__DIR__)).DS);
define('APP', ROOT.'app'.DS);
define('SYSTEM', ROOT.'system'.DS);


/** load composer autoloader */
if (file_exists(ROOT.'vendor'.DS.'autoload.php')) {
    require ROOT.'vendor'.DS.'autoload.php';
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
 */

if (defined('ENVIRONMENT')) {
    switch (ENVIRONMENT) {
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

/** initiate config */
require APP.'Config'.DS.'bootstrap.php';
