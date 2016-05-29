<?php
/**
 * Routing Filters - all standard Routing Filters are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Routing\Route;
use Helpers\Csrf;

use Support\Facades\Auth;
use Support\Facades\Redirect;
use Support\Facades\Request;
use Support\Facades\Response;
use Support\Facades\Session;


/** Define Route Filters. */

// A Testing Filter which dump the matched Route.
Route::filter('test', function($route) {
    echo '<pre style="margin: 10px;">' .var_export($route, true) .'</pre>';
});

// A simple CSRF Filter.
Route::filter('csrf', function($route) {
    $token = Request::input('csrfToken');

    if (($route->method() == 'POST') && ($token != Session::token())) {
        // When CSRF Token is invalid, respond with Error 400 Page (Bad Request)
        return Response::error(400);
    }
});

// Referer checking Filter.
Route::filter('referer', function($route) {
    // Check if the visitor come to this Route from another site.
    $referer = Request::header('referer');

    if(($referer !== null) && ! str_starts_with($referer, SITEURL)) {
        return Redirect::error(400);
    }
});

// Authentication Filters.
Route::filter('auth', function($route) {
    if (! Auth::check()) {
         // User is not logged in, redirect him to Login Page.
         return Redirect::to('login');
    }
});

Route::filter('guest', function($route) {
    if (! Auth::guest()) {
        // User is authenticated, redirect him to Dashboard Page.
        return Redirect::to('dashboard');
    }
});
