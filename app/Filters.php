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
    $usingAjax = ($request->ajax() || $request->wantsJson());

    // Retrieve the CSRF token from Request instance.
    $token = $usingAjax ? $request->header('X-CSRF-Token') : $request->input('csrfToken');

    // Retrieve the Session Store instance.
    $session = $request->session();

    if ($token != $session->token()) {
        // The CSRF Token is invalid, respond with Error 400 (Bad Request)
        if ($usingAjax) {
            return Response::make('Bad Request', 400);
        }

        App::abort(400, 'Bad Request');
    }
});

// Authentication Filters.
Route::filter('auth', function($route, $request, $guard = null)
{
    if (! Auth::guard($guard)->check()) {
        if ($request->ajax() || $request->wantsJson()) {
            return Response::make('Unauthorized Access', 401);
        }

        return Redirect::guest('login');
    }
});

Route::filter('auth.basic', function($route, $request)
{
    return Auth::basic();
});

Route::filter('guest', function($route, $request, $guard = null)
{
    if (! Auth::guard($guard)->guest()) {
        if ($request->ajax() || $request->wantsJson()) {
            return Response::make('Unauthorized Access', 401);
        }

        return Redirect::to('admin/dashboard');
    }
});
