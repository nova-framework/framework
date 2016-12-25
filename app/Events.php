<?php
/**
 * Events - all standard Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */

/** Define Events. */

// Add a Listener Closure to the Event 'router.matched'.
Event::listen('router.matched', function($route, $request) {
    // Share the Application version.
    $path = ROOTDIR .'VERSION.txt';

    if (is_readable($path)) {
        $version = file_get_contents($path);
    } else {
        $version = VERSION;
    }

    View::share('version', trim($version));

    // Share the Views the current URI.
    View::share('currentUri', $request->path());

    // Share the Views the Backend's base URI.
    $segments = $request->segments();

    if(! empty($segments)) {
        // Make the path equal with the first part if it exists, i.e. 'admin'
        $baseUri = array_shift($segments) .'/';

        // Add to path the next part, if it exists, defaulting to 'dashboard'.
        $baseUri .= ! empty($segments) ? array_shift($segments) : 'dashboard';
    } else {
        $baseUri = '';
    }

    View::share('baseUri', $baseUri);
});
