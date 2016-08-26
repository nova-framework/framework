<?php
/**
 * Events - all standard Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define Events. */

// Add a Listener Closure to the Event 'framework.controller.executing'.
Event::listen('router.matched', function($route, $request) {
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

    // Share on Views the CSRF Token.
    $session = $request->session();

    View::share('csrfToken', $session->token());

    // Run the Hooks associated to the Views.
    $hooks = Hooks::get();

    foreach (array('afterBody', 'css', 'js', 'meta', 'footer') as $hook) {
        $result = $hooks->run($hook);

        // Share the result into Views.
        View::share($hook, $result);
    }
});
