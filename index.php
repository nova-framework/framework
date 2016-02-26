<?php
/**
 * SimpleMVC specifed directory default is '.'
 * If app folder is not in the same directory update it's path.
 */
$smvc = '.';

/* Set the full path to the docroot */
define('ROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

/* Make the application relative to the docroot, for symlink'd index.php */
if (!is_dir($smvc) and is_dir(ROOT.$smvc)) {
    $smvc = ROOT.$smvc;
}

/* Define the absolute paths for configured directories */
define('SMVC', realpath($smvc).DIRECTORY_SEPARATOR);

/* Unset non used variables */
unset($smvc);

/* load composer autoloader */
if (file_exists(SMVC.'vendor/autoload.php')) {
    require SMVC.'vendor/autoload.php';
} else {
    echo '<h1>Please install via composer.json</h1>';
    echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
    echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
    exit;
}

if (!is_readable(SMVC.'app/Core/Config.php')) {
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

/* initiate config */
new Core\Config();

/** load routes */
require SMVC.'app/Core/routes.php';
