<?php
/**
 * Event - Store the information required by an Event deploying.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 19th, 2015
 */

namespace Nova\Core;


class Event
{
    private $name;
    private $params;


    public function __construct($name, $params = array())
    {
        $this->name   = $name;
        $this->params = $params;
    }

    public function name()
    {
        return $this->name;
    }

    public function params()
    {
        return $this->params;
    }

}
