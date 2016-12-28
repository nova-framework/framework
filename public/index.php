<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

//--------------------------------------------------------------------------
// Define the absolute paths for Application directories
//--------------------------------------------------------------------------

define('APPDIR', realpath(__DIR__ .'/../app/') .DS);
define('PUBLICDIR', realpath(__DIR__) .DS);
define('ROOTDIR', realpath(__DIR__ .'/../') .DS);

//--------------------------------------------------------------------------
// Load the Composer Autoloader
//--------------------------------------------------------------------------

require ROOTDIR .'vendor/autoload.php';

//--------------------------------------------------------------------------
// Bootstrap the Framework and get the Application instance
//--------------------------------------------------------------------------

$app = require_once APPDIR .'Boot' .DS .'Start.php';

//--------------------------------------------------------------------------
// Run the Application
//--------------------------------------------------------------------------

$app->run();
