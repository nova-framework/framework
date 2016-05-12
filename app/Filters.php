<?php
/**
 * Routing Filters - all standard Routing Filters are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 19th, 2016
 */

use Core\Route;
use Core\Response;
use Core\Redirect;
use Helpers\Csrf;
use Auth\Auth;


/** Define Route Filters. */

// A Testing Filter which dump the matched Route.
Route::filter('test', function($route) {
    echo '<pre>' .var_export($route, true) .'</pre>';
});

// A simple CSRF Filter.
Route::filter('csrf', function($route) {
    if (($route->method() == 'POST') && ! Csrf::isTokenValid()) {
        // When CSRF Token is invalid, respond with Error 400 Page (Bad Request)
        return Response::error(400);
    }
});

// Authentication Filters.
Route::filter('auth', function($route) {
    if (! Auth::check()) {
         // User is not logged in, respond with a Redirect code 401 (Unauthorized)
         return Redirect::to('login', 401);
    }
});

Route::filter('guest', function($route) {
    if (! Auth::guest()) {
        // User is authenticated, respond with a Redirect code 401 (Unauthorized)
        return Redirect::to('dashboard', 401);
    }
});
