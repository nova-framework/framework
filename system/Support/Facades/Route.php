<?php

namespace Nova\Support\Facades;

/**
 * @see \Nova\Routing\Router
 */
class Route extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'router'; }

}
