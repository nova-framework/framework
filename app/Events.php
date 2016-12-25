<?php
/**
 * Events - all standard Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define Events. */

// Add a Listener to the Event 'router.matched', to process the global View variables.
Event::listen('router.matched', function($route, $request) {
    // Share the Application version.
    $version = trim(file_get_contents(ROOTDIR .'VERSION.txt'));

    View::share('version', $version);

    // Share on Views the CSRF Token.
    $session = $request->session();

    View::share('csrfToken', $session->token());

    // Calculate the URIs and share them on Views.
    $uri = $request->path();

    // Prepare the base URI.
    $segments = $request->segments();

    if (! empty($segments)) {
        // Make the path equal with the first part if it exists, i.e. 'admin'
        $baseUri = array_shift($segments);

        // Add to path the next part, if it exists, defaulting to 'dashboard'.
        if (! empty($segments)) {
            $baseUri .= '/' .array_shift($segments);
        } else if ($baseUri == 'admin') {
            $baseUri .= '/dashboard';
        }
    } else {
        // Respect the URI conventions.
        $baseUri = '/';
    }

    View::share('currentUri', $uri);
    View::share('baseUri', $baseUri);
});
