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
use Nova\Utils\PriorityQueue;


class Manager
{
    private static $instance;

    private $events = array();


    public function __construct()
    {
        self::$instance =& $this;
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

    public static function sendEvent($name, $params = array(), &$result = null)
    {
        $manager = self::getInstance();

        $manager->trigger($name, $params, function($data) use (&$result) {
            if(is_array($result)) {
                $result[] = $data;
            }
            else if(is_string($result)) {
                if(! is_string($data) && ! is_integer($data)) {
                    throw new \UnexpectedValueException('Unsupported Data type while the Result is String');
                }

                $result .= $data;
            }
            else if(is_bool($result)) {
                if(! is_bool($data)) {
                    throw new \UnexpectedValueException('Unsupported Data type while the Result is Boolean');
                }

                $result = $result ? $data : false;
            }
            else if(! is_null($result)) {
                throw new \UnexpectedValueException('Unsupported Result type');
            }
        });
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
        if(! isset($this->events[$name])) {
            $this->events[$name] = new PriorityQueue();
        }

        $callbacks =& $this->events[$name];

        $callbacks->insert($callback, $priority);
    }

    public function clear($name = null)
    {
        if($name !== null) {
            unset($this->events[$name]);

            return;
        }

        $this->events = array();
    }

    /**
     * Trigger an Event deploying to the Listeners registered for it.
     *
     * @param  string $name name of the event
     * @param  array  $params array of parameters
     * @param  string $callback callback invoked after Event deploying
     */
    public function trigger($name, $params = array(), $callback = null)
    {
        // Create a new Event.
        $event = new Event($name, $params);

        // Deploy the Event notification to Listeners.
        $this->notify($event, $callback);
    }

    public function notify($event, $notifier = null)
    {
        $name = $event->name();

        if(! isset($this->events[$name])) {
            // There are no Listeners to observe this type of Event.
            return;
        }

        $callbacks = clone $this->events[$name];

        // First, preserve a instance of the Current Controller.
        $controller = Controller::getInstance();

        foreach ($callbacks as $callback) {
            // Invoke the Listener's Callback and pass the Event as parameter.
            $result = $this->invokeObject($callback, $event);

            if ($notifier) {
                // Invoke the Notifier with the previous invocation Result as parameter.
                $this->invokeNotifier($notifier, $result);
            }
        }

        // Ensure the restoration of the right Controller instance.
        $controller->setInstance();
    }

    /**
     * Invoke the Object Callback with its associated parameters.
     *
     * @param  object $callback
     * @param  object $event Event parameter
     */
    private function invokeObject($callback, $param)
    {
        if (is_object($callback)) {
            // Call the Closure.
            return call_user_func($callback, $param);
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

        // The called Method should be defined in the called Class, not in one of its parents.
        if (! in_array(strtolower($method), array_map('strtolower', get_class_methods($object)))) {
            return false;
        }

        if($object instanceof Controller) {
            // We are going to call-out a Controller; special setup is required.

            // The Controller instance should be properly initialized before executing its Method.
            $object->initialize($className, $method);
        }

        // Execute the Object's Method and return the result.
        return call_user_func(array($object, $method), $param);
    }

    /**
     * Invoke the Notifier Callback with its associated parameter.
     *
     * @param  object $callback
     * @param  object $result result parameter
     */
    private function invokeNotifier($callback, $param)
    {
        if (is_object($callback) || is_array($callback)) {
            // Call the Closure.
            return call_user_func($callback, $param);
        }

        // Call the object Class and its static Method.
        $result = explode('@', $callback);

        $className = $segments[0];
        $method    = $segments[1];

        // Check first if the Class exists.
        if (! class_exists($className)) {
            return false;
        }

        // Execute the Object's Method and return the result.
        return call_user_func(array($className, $method), $param);
    }

}
