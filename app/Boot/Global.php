<?php

use App\Modules\Platform\Models\Option;

use Nova\Auth\Access\AuthorizationException;
use Nova\Auth\AuthenticationException;
use Nova\Session\TokenMismatchException;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


//--------------------------------------------------------------------------
// Application Error Logger
//--------------------------------------------------------------------------

Log::useFiles(STORAGE_PATH .'logs' .DS .'error.log');

//--------------------------------------------------------------------------
// Application Error Handler
//--------------------------------------------------------------------------

App::error(function (Exception $e, $code)
{
    static $dontReport = array(
        'Nova\Auth\AuthenticationException',
        'Nova\Database\ORM\ModelNotFoundException',
        'Nova\Session\TokenMismatchException',
        'Nova\Validation\ValidationException',
        'Symfony\Component\HttpKernel\Exception\HttpException',
    );

    if (! in_array(get_class($e), $dontReport)) {
        Log::error($e);
    }

    // Prepare the exception.
    if ($e instanceof ModelNotFoundException) {
        $e = new NotFoundHttpException($e->getMessage(), $e);
    }

    $request = Request::instance();

    if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
        if ($e instanceof HttpException) {
            $code    = $e->getStatusCode();
            $headers = $e->getHeaders();
        } else {
            $code    = 403;
            $headers = array();
        }

        return Response::json(array('error' => $e->getMessage()), $code, $headers);
    }

    // Standard processing.
    else if ($e instanceof TokenMismatchException) {
        $except = array('password', 'password_confirmation');

        return Redirect::back()
            ->withInput($request->except($except))
            ->withStatus(__('Validation Token has expired. Please try again!'), 'danger');
    } else if ($e instanceof AuthenticationException) {
        $guards = $e->guards();

        // We will use the first exception guard.
        $guard = array_shift($guards);

        $uri = Config::get("auth.guards.{$guard}.paths.authorize", 'login');

        return Redirect::to($uri)
            ->withStatus(__('Please login to access this resource.'), 'info');
    } else if ($e instanceof AuthorizationException) {
        $guard = Config::get('auth.defaults.guard', 'web');

        $uri = Config::get("auth.guards.{$guard}.paths.dashboard", 'dashboard');

        return Redirect::to($uri)
            ->withStatus(__('You are not authorized to access this resource.'), 'danger');
    } else if ($e instanceof HttpException) {
        $code = $e->getStatusCode();

        if (View::exists('Errors/' .$code)) {
            $view = View::makeLayout('Default', 'Bootstrap')
                ->shares('title', 'Error ' .$code)
                ->nest('content', 'Errors/' .$code, array('exception' => $e));

            return Response::make($view->render(), $code, $e->getHeaders());
        }
    }
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
