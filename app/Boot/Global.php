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
    $response = Layout::make('default')
        ->shares('title', 'Error ' .$code)
        ->nest('content', 'Error/' .$code);

    // Setup propely the Content Type.
    $headers['Content-Type'] = 'text/html';

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
// Boot Stage Customization
//--------------------------------------------------------------------------

/**
 * Create a constant for the URL of the site.
 */
define('SITEURL', $app['config']['app.url']);

/**
 * Create a constant for the name of the site.
 */
define('SITETITLE', $app['config']['app.name']);

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
