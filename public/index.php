<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

/** Define the absolute paths for configured directories. */

define('APPDIR', realpath(__DIR__.'/../app/') .DS);
define('SYSTEMDIR', realpath(__DIR__.'/../system/') .DS);
define('PUBLICDIR', realpath(__DIR__) .DS);
define('ROOTDIR', realpath(__DIR__.'/../') .DS);

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
 */
define('ENVIRONMENT', 'development');

/** Composer installation check. */
if (! file_exists(ROOTDIR .'vendor/autoload.php')) {
    echo "<h1>Please install via composer.json</h1>";
    echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
    echo "<p>Once composer is installed, navigate to the working directory in your terminal/command prompt and enter 'composer install'</p>";
    exit;
}

/** Boot the Application. */
require APPDIR .'Boot' .DS .'Start.php';
