<?php

/**
 * Events - all standard Events are defined here.
 */


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
