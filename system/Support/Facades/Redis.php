<?php

namespace Support\Facades;


/**
 * @see \Redis\Database
 */
class Redis extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'redis'; }

}
