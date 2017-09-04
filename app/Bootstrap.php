<?php

use Nova\Http\Request;


/**
 * Listener Closure to the Event 'router.matched'.
 */
Event::listen('router.matched', function($route, Request $request)
{
    // Share the Views the current URI.
    View::share('currentUri', $request->path());
});
