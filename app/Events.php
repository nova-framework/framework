<?php
/**
 * Events - all standard Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 *
 */

use Nova\Http\Request;


/** Define Events. */

// Add a Listener to the Event 'router.matched', to process the global View variables.
Event::listen('base.controller.initialize', function(Request $request)
{
    // Share the Application version.
    $path = ROOTDIR .'VERSION.txt';

    if (is_readable($path)) {
        $version = trim(file_get_contents($path));
    } else {
        $version = VERSION;
    }

    View::share('version', $version);

    // Share on Views the CSRF Token and the current URI.
    View::share('csrfToken', Session::token());

    View::share('currentUri', $request->path());
});
