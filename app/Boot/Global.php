<?php

//--------------------------------------------------------------------------
// Application Error Logger
//--------------------------------------------------------------------------

Log::useFiles(storage_path() .DS .'logs' .DS .'error.log');

//--------------------------------------------------------------------------
// Application Error Handler
//--------------------------------------------------------------------------

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Session\TokenMismatchException;

use Symfony\Component\HttpKernel\Exception\HttpException;


App::error(function(Exception $exception, $code, $fromConsole)
{
    if (($exception instanceof ModelNotFoundException) || ($exception instanceof HttpException)) {
        // Do not report those exceptions.
        return;
    }

    // When CSRF mismatch.
    else if ($exception instanceof TokenMismatchException) {
        return Redirect::back()->withStatus(__('Your session expired. Please try again!'), 'danger');
    }

    Log::error($exception);

    if ($fromConsole) {
        return 'Error ' .$code .': ' .$e->getMessage()."\n";
    }

    //return '<h1>Error ' .$code .'</h1><p>' .$e->getMessage() .'</p>';
});

// Special handling for the HTTP Exceptions.
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
    $path = BASEPATH .'VERSION.txt';

    if (is_readable($path)) {
        $version = file_get_contents($path);
    } else {
        $version = VERSION;
    }

    // We'll create the templated Error Page Response.
    $response = View::makeLayout('Default')
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
// Load The Options
//--------------------------------------------------------------------------

use App\Models\Option;

if (CONFIG_STORE === 'database') {
    // Retrieve the Option items, caching them for 24 hours.
    $options = Cache::remember('system_options', 1440, function()
    {
        return Option::all();
    });

    // Setup the information stored on the Option instances into Configuration.
    foreach ($options as $option) {
        $key = $option->group;

        if (! empty($option->item)) {
            $key .= '.' .$option->item;
        }

        Config::set($key, $option->value);
    }
} else if(CONFIG_STORE !== 'files') {
    throw new \InvalidArgumentException('Invalid Config Store type.');
}

//--------------------------------------------------------------------------
// Boot Stage Customization
//--------------------------------------------------------------------------

/**
 * Create a constant for the name of the site.
 */
define('SITE_TITLE', $app['config']['app.name']);
