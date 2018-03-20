<?php

/**
 * Events - all standard Events are defined here.
 */


use Nova\Http\Request;


/** Define Events. */

// Add a Listener to the Event 'router.matched', to process the global View variables.
Event::listen('router.matched', function ($route, Request $request)
{
    View::share('currentUri', $request->path());
});


// Add a Listener to the Event 'nova.queue.looping', to check the database connection.
Event::listen('nova.queue.looping', function ($connection, $queue)
{
    if ($connection != 'database') {
        return;
    }

    try {
        $count = DB::table('jobs')->count();

        if ($count == 0) return false;
    }
    catch (Exception $e) {
        return false;
    }
});
