<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

/** Define the absolute paths for configured directories */

define('APPDIR', realpath(__DIR__.'/../app/').'/');
define('SYSTEMDIR', realpath(__DIR__.'/../system/').'/');
define('PUBLICDIR', realpath(__DIR__).'/');
define('ROOTDIR', realpath(__DIR__.'/../').'/');

/** load composer autoloader */
if (file_exists(ROOTDIR.'vendor/autoload.php')) {
    require ROOTDIR.'vendor/autoload.php';
} else {
    echo "<h1>Please install via composer.json</h1>";
    echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
    echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
    exit;
}

if (!is_readable(APPDIR.'Config.php')) {
    die('No Config.php found, configure and rename Config.example.php to Config.php in app.');
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

/** initiate Alias */
new Core\Alias();

/** initiate config */
new \App\Config();

/** load routes */
require APPDIR.'Routes.php';
