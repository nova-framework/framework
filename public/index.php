<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

/** Define the absolute paths for configured directories. */

define('APPDIR', realpath(__DIR__.'/../app/') .DS);
define('SYSTEMDIR', realpath(__DIR__.'/../system/') .DS);
define('PUBLICDIR', realpath(__DIR__) .DS);
define('ROOTDIR', realpath(__DIR__.'/../') .DS);

/** Set the Storage Path. */
define('STORAGE_PATH', APPDIR .'Storage' .DS);

/** Load the composer autoloader */
if (file_exists(ROOTDIR .'vendor/autoload.php')) {
    require ROOTDIR .'vendor/autoload.php';
} else {
    echo "<h1>Please install via composer.json</h1>";
    echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
    echo "<p>Once composer is installed, navigate to the working directory in your terminal/command prompt and enter 'composer install'</p>";
    exit;
}

if (! is_readable(APPDIR .'Config.php')) {
    die('No Config.php found, configure and rename Config.example.php to Config.php in app.');
}

/** Boot the Application. */
require APPDIR .'Boot' .DS .'Start.php';
