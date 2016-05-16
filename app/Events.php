<?php
/**
 * Events - all standard Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Event;


/** Define Events. */

// Add a testing Listener Class to the Event 'test'.
Event::listen('test', 'App\Events\Test@handle');

// Add a testing Listener Closure to the Event 'test'.
Event::listen('test', function($data) {
    echo '<pre>' .var_export($data, true) .'</pre>';
});
