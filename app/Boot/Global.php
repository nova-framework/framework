<?php

//--------------------------------------------------------------------------
// Application Error Logger
//--------------------------------------------------------------------------

Log::useFiles(storage_path() .DS .'Logs' .DS .'error.log');

//--------------------------------------------------------------------------
// Application Error Handler
//--------------------------------------------------------------------------

App::error(function(Exception $exception, $code, $fromConsole)
{
    Log::error($exception);

    if ($fromConsole) {
        return 'Error ' .$code .': ' .$e->getMessage()."\n";
    }

    //return '<h1>Error ' .$code .'</h1><p>' .$e->getMessage() .'</p>';
});

// Special handling for the HTTP Exceptions.
use Symfony\Component\HttpKernel\Exception\HttpException;

App::error(function(HttpException $exception)
{
    $code = $exception->getStatusCode();

    $headers = $exception->getHeaders();

    if (Request::ajax()) {
        // An AJAX request; we'll create a JSON Response.
        $content = array('status' => $code);

        return Response::json($content, $code, $headers);
    }

    // Retrieve first the Application version.
    $path = ROOTDIR .'VERSION.txt';

    if (is_readable($path)) {
        $version = file_get_contents($path);
    } else {
        $version = VERSION;
    }

    // We'll create the templated Error Page Response.
    $response = Layout::make('default')
        ->shares('version', trim($version))
        ->shares('title', 'Error ' .$code)
        ->nest('content', 'Error/' .$code);

    return Response::make($response, $code, $headers);
});

//--------------------------------------------------------------------------
// Application Missing Route Handler
//--------------------------------------------------------------------------
/*
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

App::missing(function(NotFoundHttpException $exception)
{
    //
});
*/
//--------------------------------------------------------------------------
// Maintenance Mode Handler
//--------------------------------------------------------------------------

App::down(function()
{
    return Response::make("Be right back!", 503);
});

//--------------------------------------------------------------------------
// Boot Stage Customization
//--------------------------------------------------------------------------

/**
 * Create a constant for the name of the site.
 */
define('SITE_TITLE', $app['config']['app.name']);

/**
 * Send a E-Mail to administrator when a Error is logged.
 */
/*
use Shared\Log\Mailer as LogMailer;

LogMailer::initHandler($app);
*/
