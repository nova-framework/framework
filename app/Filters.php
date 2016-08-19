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

// A simple CSRF Filter.
Route::filter('csrf', function($route, $request) {
    $token = $request->input('csrfToken');

    $method = $request->method();

    if (($method == 'POST') && ($token != Session::token())) {
        // When CSRF Token is invalid, respond with Error 400 Page (Bad Request)
        return Response::error(400);
    }
});

// Referer checking Filter.
Route::filter('referer', function($route, $request) {
    // Check if the visitor come to this Route from another site.
    $referer = $request->header('referer');

    if(! str_starts_with($referer, Config::get('app.url'))) {
        // When Referrer is invalid, respond with Error 400 Page (Bad Request)
        return Response::error(400);
    }
});

// Authentication Filters.
Route::filter('auth', function($route, $request) {
    if (! Auth::check()) {
         // User is not logged in, redirect him to Login Page.
         return Redirect::to('login');
    }
});

Route::filter('guest', function($route, $request) {
    if (! Auth::guest()) {
        // User is authenticated, redirect him to Dashboard Page.
        return Redirect::to('admin/dashboard');
    }
});
