<?php
/**
 * Raw Query.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 22th, 2016
 *
 * Based on Pixie Query Builder: https://github.com/usmanhalalit/pixie
 */

namespace Nova\Database\Query;


class Raw
{

    /**
     * @var string
     */
    protected $value;

    /**
     * @var array
     */
    protected $bindings;

    public function __construct($value, $bindings = array())
    {
        $this->value = (string) $value;

        $this->bindings = (array) $bindings;
    }

    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}
