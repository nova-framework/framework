<?php

/**
 * Routing Filters - all standard Routing Filters are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 *
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


/**
 * Define the Route Filters.
 */

// The CSRF Filter.
Route::filter('csrf', function($route, $request)
{
    // Retrieve the CSRF token from Request instance.
    $token = $request->ajax() ? $request->header('X-CSRF-Token') : $request->input('csrfToken');

    if ($token === Session::token()) {
        return;
    }

    // The CSRF token does not match.
    else if ($request->ajax() || $request->wantsJson()) {
        return Response::make('Bad Request', 400);
    }

    // Redirect back with error message.
    $status = __('Your session expired. Please try again!');

    return Redirect::back()->withStatus($status, 'danger');
});

// Authentication Filters.
Route::filter('auth', function ($route, $request, $guard = null)
{
    $guard = $guard ?: Config::get('auth.defaults.guard', 'web');

    if (Auth::guard($guard)->check()) {
        // User authenticated with this Guard, then we will use it as default.
        Auth::shouldUse($guard);

        return;
    }

    // The User is not authenticated.
    else if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
        return Response::json(array('error' => 'Unauthorized Access'), 401);
    }

    // Get the Guard's authorize path from configuration.
    $uri = Config::get("auth.guards.{$guard}.paths.authorize", 'login');

    return Redirect::guest($uri);
});

Route::filter('auth.basic', function ($route, $request, $guard = null)
{
    return Auth::guard($guard)->basic();
});

Route::filter('guest', function ($route, $request, $guard = null)
{
    $guard = $guard ?: Config::get('auth.defaults.guard', 'web');

    if (Auth::guard($guard)->guest()) {
        return;
    }

    // The User is authenticated.
    else if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
        return Response::json(array('error' => 'Unauthorized Access'), 401);
    }

    // Get the Guard's dashboard path from configuration.
    $uri = Config::get("auth.guards.{$guard}.paths.dashboard", 'admin/dashboard');

    return Redirect::to($uri);
});
