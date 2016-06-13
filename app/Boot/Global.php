<?php

//--------------------------------------------------------------------------
// Application Error Logger
//--------------------------------------------------------------------------

Log::useFiles(storage_path() .'Logs' .DS .'error.log');

// Send a E-Mail to administrator when a Error is logged by Application.
/*
use Monolog\Logger;

Log::getMonolog()->pushHandler(
    new Monolog\Handler\SwiftMailerHandler(
        Mailer::getSwiftMailer(),
        Swift_Message::newInstance('[Log] Errors!')
            ->setFrom('no-reply@novaframework.dev')
            ->setTo('novaframework@gmail.com'),
        Logger::ERROR, // Set minimal Log Level for Mail
        true           // Bubble to next handler?
    )
);
*/
//--------------------------------------------------------------------------
// Application Error Handler
//--------------------------------------------------------------------------

use Exception\RedirectToException;

App::error(function(Exception $exception, $code)
{
    // Do not log the Redirect Exceptions.
    if (! $exception instanceof RedirectToException) {
        Log::error($exception);
    }
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
// Try To Register Again The Config Manager
//--------------------------------------------------------------------------

use Config\Repository as ConfigRepository;
use Support\Facades\Facade;

if(APPCONFIG_STORE == 'database') {
    // Get the Database Connection instance.
    $connection = $app['db']->connection();

    // Get a fresh Config Loader instance.
    $loader = $app->getConfigLoader();

    // Setup Database Connection instance.
    $loader->setConnection($connection);

    // Refresh the Application's Config instance.
    $app->instance('config', $config = new ConfigRepository($loader));

    // Make the Facade to refresh its information.
    Facade::clearResolvedInstance('config');
} else if(APPCONFIG_STORE != 'files') {
    throw new \InvalidArgumentException('Invalid Config Store type.');
}

// Refresh the Modules configuration.
$modules = $app['config']['modules'];

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
