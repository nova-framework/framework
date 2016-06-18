<?php
/**
 * Routing Filters - all standard Routing Filters are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define Route Filters. */

// A Testing Filter which dump the matched Route.
Router::filter('test', function($route) {
    echo '<pre style="margin: 10px;">' .var_export($route, true) .'</pre>';
});

// A simple CSRF Filter.
Router::filter('csrf', function($route) {
    $token = Request::input('csrfToken');

    $method = Request::method();

    if (($method == 'POST') && ($token != Session::token())) {
        // When CSRF Token is invalid, respond with Error 400 Page (Bad Request)
        return Response::error(400);
    }
});

// Referer checking Filter.
Router::filter('referer', function($route) {
    // Check if the visitor come to this Route from another site.
    $referer = Request::header('referer');

    if(! str_starts_with($referer, Config::get('app.url'))) {
        // When Referrer is invalid, respond with Error 400 Page (Bad Request)
        return Response::error(400);
    }
});

// Authentication Filters.
Router::filter('auth', function($route) {
    if (! Auth::check()) {
         // User is not logged in, redirect him to Login Page.
         return Redirect::to('login');
    }
});

// Role-based Authorization Filter.
Router::filter('roles', function($route) {
    $action = $route->getAction();

    $roles = array_get($action, 'roles');

    if (! is_null($roles) && ! Auth::user()->hasRole($roles)) {
         $status = __('You are not authorized to access this resource.');

         return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
    }
});

Router::filter('guest', function($route) {
    if (! Auth::guest()) {
        // User is authenticated, redirect him to Dashboard Page.
        return Redirect::to('admin/dashboard');
    }
});
