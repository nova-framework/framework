<?php

//--------------------------------------------------------------------------
// Application Error Logger
//--------------------------------------------------------------------------

Log::useFiles(storage_path() .DS .'Logs' .DS .'error.log');

//--------------------------------------------------------------------------
// Application Error Handler
//--------------------------------------------------------------------------

// The standard handling of the Exceptions.
App::error(function(Exception $exception, $code)
{
    Log::error($exception);
});

// Special handling for the HTTP Exceptions.
use Symfony\Component\HttpKernel\Exception\HttpException;

App::error(function(HttpException $exception)
{
    $code = $exception->getStatusCode();

    $headers = $exception->getHeaders();

    if ($code == 500) {
        // We should log the Error 500 Exceptions.
        Log::error($exception);
    }

    if (Request::ajax()) {
        // An AJAX request; we'll create a JSON Response.
        $content = array('status' => $code);

        // Setup propely the Content Type.
        $headers['Content-Type'] = 'application/json';

        return Response::json($content, $code, $headers);
    }

    // We'll create the templated Error Page Response.
    $response = Template::make('default')
        ->shares('title', 'Error ' .$code)
        ->nest('content', 'Error/' .$code);

    // Setup propely the Content Type.
    $headers['Content-Type'] = 'text/html';

    return Response::make($response, $code, $headers);
});

//--------------------------------------------------------------------------
// Try To Register Again The Config Manager
//--------------------------------------------------------------------------

use Nova\Config\Repository as ConfigRepository;
use Nova\Support\Facades\Facade;

if(CONFIG_STORE == 'database') {
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
} else if(CONFIG_STORE != 'files') {
    throw new \InvalidArgumentException('Invalid Config Store type.');
}

//--------------------------------------------------------------------------
// Boot Stage Customization
//--------------------------------------------------------------------------

/**
 * Create a constant for the URL of the site.
 */
define('SITEURL', $app['config']['app.url']);

/**
 * Define relative base path.
 */
define('DIR', $app['config']['app.path']);

/**
 * Create a constant for the name of the site.
 */
define('SITETITLE', $app['config']['app.name']);

/**
 * Set a default language.
 */
define('LANGUAGE_CODE', $app['config']['app.locale']);

/**
 * Set the default template.
 */
define('TEMPLATE', $app['config']['app.template']);

/**
 * Set a Site administrator email address.
 */
define('SITEEMAIL', $app['config']['app.email']);

/**
 * Send a E-Mail to administrator (defined on SITEEMAIL) when a Error is logged.
 */
/*
use Shared\Log\Mailer as LogMailer;

LogMailer::initHandler($app);
*/
