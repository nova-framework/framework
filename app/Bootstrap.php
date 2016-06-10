<?php
/**
 * Bootstrap handler - perform the Application's bootstrap stage.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Config\LoaderManager;
use Config\Repository as Config;
use Http\Request;
use Support\Facades\Facade;

//--------------------------------------------------------------------------
// Set PHP Error Reporting Options
//--------------------------------------------------------------------------

error_reporting(-1);

//--------------------------------------------------------------------------
// Create New Application
//--------------------------------------------------------------------------

$app = new Application();

$app->instance('app', $app);

//$app->bindInstallPaths($paths);

//--------------------------------------------------------------------------
// Load the Facades
//--------------------------------------------------------------------------

Facade::setFacadeApplication($app);

//--------------------------------------------------------------------------
// Register The Config Manager
//--------------------------------------------------------------------------

$loader = new LoaderManager();

$app->instance('config', new Config($loader));

//--------------------------------------------------------------------------
// Application Error Logger
//--------------------------------------------------------------------------

Log::useFiles(storage_path() .'/Logs/framework.log');

//--------------------------------------------------------------------------
// Application Error Handler
//--------------------------------------------------------------------------

App::error(function(Exception $exception, $code)
{
        Log::error($exception);
});
