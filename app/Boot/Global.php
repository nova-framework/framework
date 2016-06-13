<?php

//--------------------------------------------------------------------------
// Application Error Logger
//--------------------------------------------------------------------------

Log::useFiles(storage_path() .'Logs' .DS .'error.log');

// Send a E-Mail to administrator when a Error is logged by Application.
/*
Log::getMonolog()->pushHandler(
    new Monolog\Handler\SwiftMailerHandler(
        Mail::getSwiftMailer(),
        Swift_Message::newInstance('[Log] Some Subject')->setFrom('from@domain.dev')->setTo('to@domain.dev'),
        Logger::ERROR, // Set minimal Log Level for Mail
        true           // Bubble to next handler?
    )
);
*/

//--------------------------------------------------------------------------
// Application Error Handler
//--------------------------------------------------------------------------

App::error(function(Exception $exception, $code)
{
    Log::error($exception);
});

//--------------------------------------------------------------------------
// Application Finish Handler
//--------------------------------------------------------------------------

use Http\ResponseProcessor;
use Session\SessionGuard;

App::finish(function($request, $response) use ($app)
{
    // Save the Session Store and cleanup its files.
    SessionGuard::handle($app);

    // Post-process the Response.
    ResponseProcessor::handle($app, $response);
});

//--------------------------------------------------------------------------
// Require The Events File
//--------------------------------------------------------------------------

require app_path() .'Events.php';

// Load the Events defined on Modules.
foreach ($modules as $module) {
    $path = app_path() .'Modules' .DS .$module .DS .'Events.php';

    if (is_readable($path)) require $path;
}

//--------------------------------------------------------------------------
// Require The Filters File
//--------------------------------------------------------------------------

require app_path() .'Filters.php';

// Load the Filters defined on Modules.
foreach ($modules as $module) {
    $path = app_path() .'Modules' .DS .$module .DS .'Filters.php';

    if (is_readable($path)) require $path;
}
