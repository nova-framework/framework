<?php
/**
 * Events - all standard Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Core\View;
use Helpers\Hooks;
use Forensics\Console;


/** Define Events. */

// Add a Listener Class to the Event 'test'.
Event::listen('test', 'App\Events\Test@handle');

// Add a Listener Closure to the Event 'test'.
Event::listen('test', function($data) {
    return '<pre>Closure : ' .var_export($data, true) .'</pre>';
});

// Add a Listener Closure to the Event 'framework.controller.executing'.
Event::listen('router.matched', function($route, $request) {
    // Run the Hooks associated to the Views.
    $hooks = Hooks::get();

    foreach (array('afterBody', 'css', 'js', 'meta', 'footer') as $hook) {
        $result = $hooks->run($hook);

        // Share the result into Views.
        View::share($hook, $result);
    }
});

// Add a Listener Closure to the Event 'nova.framework.booting'.
Event::listen('nova.framework.booting', function() {
    Console::logSpeed("Nova Framework booting");
});
