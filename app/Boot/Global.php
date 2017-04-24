<?php

//--------------------------------------------------------------------------
// Application Error Logger
//--------------------------------------------------------------------------

Log::useFiles(STORAGE_PATH .'logs' .DS .'error.log');

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

    if (Request::ajax() || Request::wantsJson()) {
        // An AJAX request; we'll create a JSON Response.
        $content = array('status' => $code);

        return Response::json($content, $code, $headers);
    }

    // We'll create the templated Error Page Response.
    $content = View::makeLayout('Default', 'Bootstrap')
        ->shares('title', 'Error ' .$code)
        ->nest('content', 'Error/' .$code);

    return Response::make($content, $code, $headers);
});

//--------------------------------------------------------------------------
// Maintenance Mode Handler
//--------------------------------------------------------------------------

App::down(function()
{
    return Response::make("Be right back!", 503);
});

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
 * Set the default theme.
 */
define('THEME', $app['config']['app.theme']);

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
