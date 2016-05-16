<?php
/**
 * Events - all standard Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Event;


/** Define Events. */

// Add a Listener to the Event 'test'.
Event::listen('test', 'App\Events\Test@handle');
