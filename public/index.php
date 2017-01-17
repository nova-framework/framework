<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

//--------------------------------------------------------------------------
// Define the absolute paths for Application directories
//--------------------------------------------------------------------------

define('BASEPATH', realpath(__DIR__ .'/../') .DS);

define('WEBPATH', realpath(__DIR__) .DS);

define('APPPATH', BASEPATH .'app' .DS);

//--------------------------------------------------------------------------
// Load the Composer Autoloader
//--------------------------------------------------------------------------

require BASEPATH .'vendor/autoload.php';

//--------------------------------------------------------------------------
// Bootstrap the Framework and get the Application instance
//--------------------------------------------------------------------------

$app = require_once APPPATH .'Boot' .DS .'Start.php';

//--------------------------------------------------------------------------
// Run the Application
//--------------------------------------------------------------------------

$app->run();
