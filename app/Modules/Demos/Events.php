<?php
/**
 * Events - all Module's specific Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define Events. */

// Add a Listener Class to the Event 'test'.
Event::listen('test', 'App\Modules\Demos\Events\Test@handle');

// Add a Listener Closure to the Event 'test'.
Event::listen('test', function($data) {
    return '<pre>Closure : ' .var_export($data, true) .'</pre>';
});
