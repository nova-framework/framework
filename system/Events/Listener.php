<?php
/**
 * Listener - Store the information who represent an Event Listener.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 21th, 2015
 */

namespace Nova\Events;

/**
 * Listener Object
 */
class Listener
{
    private $name;
    private $callback;
    private $priority;

    /**
     * Listener constructor.
     * @param string $name
     * @param callable $callback
     * @param int $priority
     */
    public function __construct($name, $callback, $priority = 0)
    {
        $this->name    = $name;
        $this->callback = $callback;
        $this->priority = $priority;
    }

    /**
     * Get name
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get callback
     * @return callable
     */
    public function callback()
    {
        return $this->callback;
    }

    /**
     * Get priority
     * @return int
     */
    public function priority()
    {
        return $this->priority;
    }
}
