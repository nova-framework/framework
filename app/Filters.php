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

    if (Session::token() == $token) {
        // The CSRF token match; nothing to do.
        return;
    }

    if ($request->ajax() || $request->wantsJson()) {
        return Response::make('Bad Request', 400);
    }

    // Redirect back with error message.
    $status = __('Your session expired. Please try again!');

    return Redirect::back()->withStatus($status, 'danger');
});

// Authentication Filters.
Route::filter('auth', function($route, $request, $guard = null)
{
    // Get the requested Authentication Guard instance.
    $instance = Auth::guard($guard);

    if ($instance->check()) {
        // The User is authenticated; nothing to do.
        return;
    }

    if ($request->ajax() || $request->wantsJson()) {
        return Response::make('Unauthorized Access', 401);
    }

    return Redirect::guest('login');
});

Route::filter('auth.basic', function($route, $request)
{
    return Auth::basic();
});

Route::filter('guest', function($route, $request, $guard = null)
{
    // Get the requested Authentication Guard instance.
    $instance = Auth::guard($guard);

    if ($instance->guest()) {
        // The User is not authenticated; nothing to do.
        return;
    }

    if ($request->ajax() || $request->wantsJson()) {
        return Response::make('Unauthorized Access', 401);
    }

    return Redirect::to('admin/dashboard');
});
