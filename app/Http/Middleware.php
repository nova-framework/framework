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
Route::middleware('referer', 'App\Http\Middleware\CheckForReferer');
