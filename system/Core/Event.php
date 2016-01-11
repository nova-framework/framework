<?php
/**
 * Event - Store the information required by an Event deploying.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 19th, 2015
 */

namespace Nova\Core;

/**
 * Event Object Class
 */
class Event
{
    private $name;
    private $params;

    /**
     * Event constructor.
     * @param string $name
     * @param array $params
     */
    public function __construct($name, $params = array())
    {
        $this->name   = $name;
        $this->params = $params;
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
     * Get params
     * @return array
     */
    public function params()
    {
        return $this->params;
    }
}
