<?php
/**
 * Routing Filters - all standard Routing Filters are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
    //
});


App::after(function($request, $response)
{
    //
});


/** Define Route Filters. */

// The CSRF Filter.
Route::filter('csrf', function($route, $request)
{
    // Retrieve the CSRF token from Request instance.
    $token = $request->ajax() ? $request->header('X-CSRF-Token') : $request->input('csrfToken');

    if ($token === Session::token()) {
        // The CSRF token match.
        return;
    } else if ($request->ajax() || $request->wantsJson()) {
        return Response::make('Bad Request', 400);
    }

    // Redirect back with error message.
    $status = __('Your session expired. Please try again!');

    return Redirect::back()->withStatus($status, 'danger');
});

// Authentication Filters.
Route::filter('auth', function($route, $request, $guard = null)
{
    $guard = $guard ?: Config::get('auth.defaults.guard', 'web');

    if (Auth::guard($guard)->check()) {
        // The User is authenticated.
        return;
    } else if ($request->ajax() || $request->wantsJson()) {
        return Response::make('Unauthorized Access', 401);
    }

    // Get the Guard's paths from configuration.
    $paths = Config::get("auth.guards.{$guard}.paths", array(
        'authorize' => 'login',
        'nonintend' => array(
            'logout',
        ),
    ));

    if (in_array($request->path(), $paths['nonintend'])) {
        return Redirect::to($paths['authorize']);
    }

    return Redirect::guest($paths['authorize']);
});

Route::filter('auth.basic', function($route, $request)
{
    return Auth::basic();
});

Route::filter('guest', function($route, $request, $guard = null)
{
    $guard = $guard ?: Config::get('auth.defaults.guard', 'web');

    if (Auth::guard($guard)->guest()) {
        // The User is not authenticated.
        return;
    } else if ($request->ajax() || $request->wantsJson()) {
        return Response::make('Unauthorized Access', 401);
    }

    // Get the Guard's paths from configuration.
    $paths = Config::get("auth.guards.{$guard}.paths", array(
        'dashboard' => 'admin/dashboard'
    ));

    return Redirect::to($paths['dashboard']);
});
