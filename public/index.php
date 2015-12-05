<?php
/**
 * SimpleMVC specifed directory default is '.'
 * If app folder is not in the same directory update it's path
 */
$smvc = '../app/';
$system = '../system/';
$root = '../';

/** Define the absolute paths for configured directories */
define('SMVC', realpath($smvc).DIRECTORY_SEPARATOR);
define('SYSTEM', realpath($system).DIRECTORY_SEPARATOR);

/** Unset non used variables */
unset($smvc);
unset($system);

/** load composer autoloader */
if (file_exists($root.'vendor/autoload.php')) {
    require $root.'vendor/autoload.php';
} else {
    echo "<h1>Please install via composer.json</h1>";
    echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
    echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
    exit;
}

if (!is_readable(SYSTEM.'/Core/Config.php')) {
    die('No Config.php found, configure and rename Config.example.php to Config.php in app/Core.');
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
new \Core\Config();

/** load routes */
require SYSTEM.'Core/routes.php';
