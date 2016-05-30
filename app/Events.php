<?php
/**
 * Events - all standard Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Support\Facades\Event;
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
Event::listen('framework.controller.executing', function($instance, $method, $params) {
    // Run the Hooks associated to the Views.
    $hooks = Hooks::get();

    foreach (array('afterBody', 'css', 'js') as $hook) {
        $result = $hooks->run($hook);

        // Share the result into Views.
        View::share($hook, $result);
    }
});

// Add a Listener Closure to the Event 'framework.controller.executing'.
Event::listen('framework.controller.executing', function($instance, $method, $params) {
    $className = get_class($instance);

    Console::log("Executing '$className@$method'");
});
