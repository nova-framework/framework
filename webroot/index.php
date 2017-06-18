<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

//--------------------------------------------------------------------------
// Set The Framework Starting Time
//--------------------------------------------------------------------------

define('FRAMEWORK_START', microtime(true));

//--------------------------------------------------------------------------
// Define the absolute paths for Application directories
//--------------------------------------------------------------------------

define('BASEPATH', realpath(__DIR__ .'/../') .DS);

define('WEBPATH', realpath(__DIR__) .DS);

define('APPPATH', BASEPATH .'app' .DS);

//--------------------------------------------------------------------------
// Load the Composer Autoloader
//--------------------------------------------------------------------------

require BASEPATH .'vendor' .DS .'autoload.php';

//--------------------------------------------------------------------------
// Bootstrap the Framework and get the Application instance
//--------------------------------------------------------------------------

$app = require_once APPPATH .'Boot' .DS .'Start.php';

//--------------------------------------------------------------------------
// Run the Application
//--------------------------------------------------------------------------

use Nova\Http\Request;


$kernel = $app->make('Nova\Http\Contracts\KernelInterface');

$response = $kernel->handle(
	$request = Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
