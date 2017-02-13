<?php
/**
 * Routing Middleware - all standard Routing Middleware are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


// The CSRF Filter.
Route::middleware('csrf',  'App\Http\Middleware\VerifyCsrfToken');

// Authentication Filters.
Route::middleware('auth',  'App\Http\Middleware\Authenticate');

Route::middleware('guest', 'App\Http\Middleware\RedirectIfAuthenticated');

Route::middleware('auth.basic', 'Nova\Auth\Middleware\AuthenticateWithBasicAuth');

// Referer checking Filter.
Route::middleware('referer', function($request, $next)
{
    // Check if the visitor come to this Route from another site.
    $referer = $request->header('referer');

    if(! starts_with($referer, Config::get('app.url'))) {
        // When Referrer is invalid, respond with Error 400 (Bad Request)
        App::abort(400, 'Bad Request');
    }

    return $next($request);
});
