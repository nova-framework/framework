<?php

namespace Shared\Hooks;

use Nova\Support\Facades\App;

use Closure;


abstract class ActionHookDispatcher
{

    /**
     * Holds the event listeners
     * @var array
     */
    protected $listeners = array();

    /**
     * The sorted event listeners.
     *
     * @var array
     */
    protected $sorted = array();

    /**
     * The event firing stack.
     *
     * @var array
     */
    protected $firing = array();


    /**
     * Adds a listener
     *
     * @param string $hook Hook name
     * @param mixed $callback Function to execute
     * @param integer $priority Priority of the action
     * @param integer $arguments Number of arguments to accept
     * @return void
     */
    public function listen($hook, $callback, $priority = 20, $arguments = 1)
    {
        $this->listeners[$hook][$priority][] = compact('callback', 'arguments');

        unset($this->sorted[$hook]);
    }

    /**
     * Fires a new action
     *
     * @param  string $action Name of action
     * @param  array $args Arguments passed to the action
     * @return void
     */
    abstract function fire($action, $args);

    /**
     * Get the event that is currently firing.
     *
     * @return string
     */
    public function firing()
    {
        return last($this->firing);
    }

    /**
     * Gets a sorted list of all listeners
     *
     * @param  string  $hook
     * @return array
     */
    public function getListeners($hook)
    {
        if (! isset($this->sorted[$hook])) {
            $this->sortListeners($hook);
        }

        return $this->sorted[$hook];
    }

    /**
     * Sort the listeners for a given event by priority.
     *
     * @param  string  $hook
     * @return array
     */
    protected function sortListeners($hook)
    {
        $this->sorted[$hook] = array();

        if (isset($this->listeners[$hook])) {
            uksort($this->listeners[$hook], function ($param1, $param2)
            {
                return strnatcmp($param1, $param2);
            });

            $this->sorted[$hook] = call_user_func_array(
                'array_merge', $this->listeners[$hook]
            );
        }
    }

    /**
     * Resolve the given callback
     *
     * @param  mixed $callback Callback
     * @return mixed A closure, an array if "class@method" or a string if "function_name"
     */
    protected function resolveCallback($callback)
    {
        if (($callback instanceof Closure) || is_array($callback)) {
            return $callback;
        }

        // If the callback is a string, we will try to resolve the class based ones.
        else if (is_string($callback)) {
            if (strpos($callback, '@') === false) {
                return $callback;
            }

            list ($class, $method) = explode('@', $callback);

            $instance = App::make('\\' . $class);

            return array($instance, $method);
        }

        return false;
    }
}
