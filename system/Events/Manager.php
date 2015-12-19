<?php
/**
 * Events Manager - Manage the Events and dispatch them to listeners.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 19th, 2015
 */

namespace Nova\Events;

use Nova\Core\Controller;
use Nova\Core\Event;


class Manager
{
    private static $instance;

    private $events;


    public function __construct()
    {
        self::$instance =& $this;

        $this->events = new SplPriorityQueue;
    }

    public static function &getInstance()
    {
        if (! self::$instance) {
            $manager = new self();
        }
        else {
            $manager =& self::$instance;
        }

        return $manager;
    }

    public static function addEvent($name, $callback, $priority = 0)
    {
        $manager = self::getInstance();

        $manager->attach($name, $callback, $priority);
    }

    /**
     * Attach the Callback with its associated Event parameter.
     *
     * @param  object $callback
     * @param  array  $params array of matched parameters
     * @param  string $message
     */
    public function attach($name, $callback, $priority = 0)
    {
        $this->events->insert(array($name, $callback), $priority);
    }

    /**
     * Trigger an Event deploying to the Listeners registered for it.
     *
     * @param  string $name name of the event
     * @param  array  $params array of parameters
     * @param  string $notifier callback invoked after Event deploying
     */
    public function trigger($name, $params = array(), $notifier = null)
    {
        foreach ($this->events as $eventInfo) {
            if ($eventInfo[0] != $name) {
                // Event Name not match; continue.
                continue;
            }

            $callback = $eventInfo[1];

            $event = new Event($name, $params);

            $result = invokeObject($callback, $event);

            if ($notifier === null) {
                continue;
            }

            invokeNotifier($notifier, $result);
        }
    }

    /**
     * Invoke the Object Callback with its associated Event parameter.
     *
     * @param  object $callback
     * @param  object $event Event parameter
     */
    private function invokeObject($callback, $event)
    {
        if (is_object($callback)) {
            // Call the Closure.
            return call_user_func($callback, $event);
        }

        // Call the object Class and its Method.
        $segments = explode('@', $callback);

        $className = $segments[0];
        $method    = $segments[1];

        // Check first if the Class exists.
        if (!class_exists($className)) {
            return false;
        }

        // Initialize the Class.
        $object = new $className();

        if($object instanceof Controller) {
            // We are going to call-out a Controller; special setup is required.

            // The called Method should be defined in the called Controller, not in one of its parents.
            if (! in_array(strtolower($method), array_map('strtolower', get_class_methods($object)))) {
                return false;
            }

            // The Controller instance should be properly initialized before executing its Method.
            $object->initialize($className, $method);
        }

        // Execute the Object's Method and return the result.
        return call_user_func(array($object, $method), $event);
    }

    /**
     * Invoke the Notifier Callback with its associated parameter.
     *
     * @param  object $callback
     * @param  object $result result parameter
     */
    private function invokeNotifier($callback, $result)
    {
        if (is_object($callback) || is_array($callback)) {
            // Call the Closure.
            return call_user_func($callback, $result);
        }

        // Call the object Class and its static Method.
        $result = explode('@', $callback);

        $className = $segments[0];
        $method    = $segments[1];

        // Check first if the Class exists.
        if (!class_exists($className)) {
            return false;
        }

        // Execute the Object's Method and return the result.
        return call_user_func(array($className, $method), $result);
    }

}
