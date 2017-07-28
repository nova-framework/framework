<?php

use Nova\Http\Request;


/**
 * Request's Referer Middleware.
 */
Route::middleware('referer', function(Request $request, Closure $next)
{
    $referrer = $request->header('referer');

    if (! Str::startsWith($referrer, Config::get('app.url'))) {
        return Redirect::back();
    }

    return $next($request);
});


/**
 * Listener Closure to the Event 'router.matched'.
 */
Event::listen('router.matched', function($route, Request $request)
{
    // Share the Views the current URI.
    View::share('currentUri', $request->path());
});
