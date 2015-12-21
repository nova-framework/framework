<?php
/**
 * Listener - Store the information who represent an Event Listener.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 21th, 2015
 */

namespace Nova\Events;


class Listener
{
    private $name;
    private $callback;
    private $priority;


    public function __construct($name, $callback, $priority = 0)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->priority = $priority;
    }

    public function name()
    {
        return $this->name;
    }

    public function callback()
    {
        return $this->callback;
    }

    public function priority()
    {
        return $this->priority;
    }

}
