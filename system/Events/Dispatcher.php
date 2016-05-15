<?php
/**
 * Dispatcher - A simple Events Dispatcher.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Events;


class Dispatcher
{
    /**
     * The active Dispatcher instance.
     *
     * @var \Events\Dispatcher
     */
    private static $instance;

    /**
     * The registered Event listeners.
     *
     * @var array
     */
    protected $listeners = array();

    /**
     * The wildcard listeners.
     *
     * @var array
     */
    protected $wildcards = array();

    /**
     * The sorted Event listeners.
     *
     * @var array
     */
    protected $sorted = array();

    /**
     * Get the Dispatcher instance.
     *
     * @return \Events\Dispatcher
     */
    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Register an Event Listener with the Dispatcher.
     *
     * @param  string|array  $event
     * @param  mixed   $listener
     * @param  int     $priority
     * @return void
     */
    public function listen($events, $listener, $priority = 0)
    {
        foreach ((array) $events as $event) {
            if (str_contains($event, '*')) {
                return $this->setupWildcardListen($event, $listener);
            }

            $this->listeners[$event][$priority][] = $this->makeListener($listener);

            unset($this->sorted[$event]);
        }
    }

    /**
     * Setup a wildcard Listener callback.
     *
     * @param  string  $event
     * @param  mixed   $listener
     * @return void
     */
    protected function setupWildcardListen($event, $listener)
    {
        $this->wildcards[$event][] = $this->makeListener($listener);
    }

    /**
     * Determine if a given Event has listeners.
     *
     * @param  string  $eventName
     * @return bool
     */
    public function hasListeners($eventName)
    {
        return isset($this->listeners[$eventName]);
    }

    /**
     * Fire an Event until the first non-null response is returned.
     *
     * @param  string  $event
     * @param  array   $payload
     * @return mixed
     */
    public function until($event, $payload = array())
    {
        return $this->fire($event, $payload, true);
    }

    /**
     * Fire an Event and call the listeners.
     *
     * @param  string  $event
     * @param  mixed   $payload
     * @param  bool    $halt
     * @return array|null
     */
    public function fire($event, $payload = array(), $halt = false)
    {
        $responses = array();

        if (! is_array($payload)) {
            $payload = array($payload);
        }

        foreach ($this->getListeners($event) as $listener) {
            $response = call_user_func_array($listener, $payload);

            if (! is_null($response) && $halt) {
                return $response;
            }

            if ($response === false) break;

            $responses[] = $response;
        }

        return $halt ? null : $responses;
    }

    /**
     * Get all of the listeners for a given Event name.
     *
     * @param  string  $eventName
     * @return array
     */
    public function getListeners($eventName)
    {
        $wildcards = $this->getWildcardListeners($eventName);

        if (! isset($this->sorted[$eventName])) {
            $this->sortListeners($eventName);
        }

        return array_merge($this->sorted[$eventName], $wildcards);
    }

    /**
     * Get the wildcard listeners for the Event.
     *
     * @param  string  $eventName
     * @return array
     */
    protected function getWildcardListeners($eventName)
    {
        $wildcards = array();

        foreach ($this->wildcards as $key => $listeners) {
            if (str_is($key, $eventName)) {
                $wildcards = array_merge($wildcards, $listeners);
            }
        }

        return $wildcards;
    }

    /**
     * Sort the listeners for a given Event by priority.
     *
     * @param  string  $eventName
     * @return array
     */
    protected function sortListeners($eventName)
    {
        $this->sorted[$eventName] = array();

        if (! isset($this->listeners[$eventName])) {
            return;
        }

        krsort($this->listeners[$eventName]);

        $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
    }

    /**
     * Register an Event listener with the dispatcher.
     *
     * @param  mixed  $listener
     * @return mixed
     */
    public function makeListener($listener)
    {
        if (is_string($listener)) {
            return $this->createClassListener($listener);
        }

        return $listener;
    }

    /**
     * Create a Class based Listener.
     *
     * @param  string  $listener
     * @return \Closure
     */
    public function createClassListener($listener)
    {
        return function() use ($listener) {
            $data = func_get_args();

            // Explode the Listener string.
            $segments = explode('@', $listener);

            $className = array_shift($segments);

            $method = ! empty($segments) ? reset($segments) : 'handle';

            return call_user_func_array(array($className, $method), $data);
        };
    }

    /**
     * Remove a set of listeners from the Dispatcher.
     *
     * @param  string  $event
     * @return void
     */
    public function forget($event)
    {
        unset($this->listeners[$event]);

        unset($this->sorted[$event]);
    }
}
