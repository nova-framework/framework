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

// A Testing Filter which dump the matched Route.
Router::filter('test', function($route, $request) {
    echo '<pre style="margin: 10px;">' .var_export($route, true) .'</pre>';
});

// A simple CSRF Filter.
Router::filter('csrf', function($route, $request) {
    $token = $request->input('csrfToken');

    $method = $request->method();

    if (($method == 'POST') && ($token != Session::token())) {
        // When CSRF Token is invalid, respond with Error 400 Page (Bad Request)
        return Response::error(400);
    }
});

// Referer checking Filter.
Router::filter('referer', function($route, $request) {
    // Check if the visitor come to this Route from another site.
    $referer = $request->header('referer');

    if(! str_starts_with($referer, Config::get('app.url'))) {
        // When Referrer is invalid, respond with Error 400 Page (Bad Request)
        return Response::error(400);
    }
});

// Authentication Filters.
Router::filter('auth', function($route, $request) {
    if (! Auth::check()) {
         // User is not logged in, redirect him to Login Page.
         return Redirect::to('login');
    }
});

// Role-based Authorization Filter.
Router::filter('roles', function($route, $request) {
    $action = $route->getAction();

    $roles = array_get($action, 'roles');

    if (! is_null($roles) && ! Auth::user()->hasRole($roles)) {
         $status = __('You are not authorized to access this resource.');

         return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
    }
});

Router::filter('guest', function($route, $request) {
    if (! Auth::guest()) {
        // User is authenticated, redirect him to Dashboard Page.
        return Redirect::to('admin/dashboard');
    }
});
