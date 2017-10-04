<?php

use App\Models\Option;

use Symfony\Component\HttpKernel\Exception\HttpException;


//--------------------------------------------------------------------------
// Application Error Logger
//--------------------------------------------------------------------------

Log::useFiles(STORAGE_PATH .'logs' .DS .'error.log');

//--------------------------------------------------------------------------
// Application Error Handler
//--------------------------------------------------------------------------

App::error(function (Exception $e, $code)
{
    Log::error($e);
});

App::error(function (HttpException $e, $code)
{
    $code = $e->getStatusCode();

    if (Request::ajax() || Request::wantsJson() || Request::is('api/*')) {
        // An AJAX request; we'll create and return a JSON Response.
        return Response::json(array('error' => $e->getMessage()), $code, $e->getHeaders());
    }

    // We'll create and return a themed Error Page as response.
    $view = View::makeLayout('Default', 'Bootstrap')
        ->shares('title', 'Error ' .$code)
        ->nest('content', 'Errors/' .$code, array('exception' => $e));

    return Response::make($view->render(), $code, $e->getHeaders());
});

//--------------------------------------------------------------------------
// Maintenance Mode Handler
//--------------------------------------------------------------------------

App::down(function ()
{
    return Response::make("Be right back!", 503);
});

//--------------------------------------------------------------------------
// Load The Options
//--------------------------------------------------------------------------

if (CONFIG_STORE === 'database') {
    // Retrieve the Option items, caching them for 24 hours.
    $options = Cache::remember('system_options', 1440, function ()
    {
        return Option::getResults();
    });

    // Setup the information stored on the Option instances into Configuration.
    foreach ($options as $option) {
        list ($key, $value) = $option->getConfigItem();

        Config::set($key, $value);
    }
}

// If the CONFIG_STORE is not in 'files' mode, go Exception.
else if(CONFIG_STORE !== 'files') {
    throw new InvalidArgumentException('Invalid Config Store type.');
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
