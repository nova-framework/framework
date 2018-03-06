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
Event::listen('router.matched', function($route, Request $request)
{
    // Share the Application version.
    $path = BASEPATH .'VERSION.txt';

    if (is_readable($path)) {
        $version = trim(file_get_contents($path));
    } else {
        $version = VERSION;
    }

    View::share('version', $version);

    View::share('currentUri', $request->path());
});


// Add a Listener to the Event 'nova.queue.looping', to check the database connection.
Event::listen('nova.queue.looping', function($connection, $queue)
{
    if ($connection != 'database') {
        return;
    }

    try {
        $count = DB::table('jobs')->count();

        if ($count == 0) {
            return false;
        }
    }
    catch (Exception $e) {
        return false;
    }
});
