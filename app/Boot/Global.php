<?php

use App\Modules\Platform\Models\Option;

use Nova\Auth\Access\AuthorizationException;
use Nova\Auth\AuthenticationException;
use Nova\Session\TokenMismatchException;

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

App::error(function (TokenMismatchException $e, $code)
{
    $input = Input::except(array('password', 'password_confirmation'));

    return Redirect::back()
        ->withInput($input)
        ->withStatus(__('Validation Token has expired. Please try again!'), 'danger');
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

App::error(function (AuthorizationException $e, $code)
{
    if (Request::ajax() || Request::wantsJson() || Request::is('api/*')) {
        // On an AJAX Request; we return a response: Error 403 (Access denied)
        return Response::make(array('error' => $e->getMessage()), 403);
    }

    // Get the Guard's dashboard path from configuration.
    $guard = Config::get('auth.defaults.guard', 'web');

    $uri = Config::get("auth.guards.{$guard}.paths.dashboard", 'dashboard');

    return Redirect::to($uri)
        ->withStatus(__('You are not authorized to access this resource.'), 'warning');
});

App::error(function (AuthenticationException $e, $code)
{
    if (Request::ajax() || Request::wantsJson() || Request::is('api/*')) {
        // On an AJAX Request; we return a response: Error 403 (Access denied)
        return Response::make(array('error' => $e->getMessage(), 'guards' => $e->guards()), 403);
    }

    // Get the Guard's dashboard path from configuration.
    $guard = Config::get('auth.defaults.guard', 'web');

    $uri = Config::get("auth.guards.{$guard}.paths.authorize", 'login');

    return Redirect::to($uri);
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
