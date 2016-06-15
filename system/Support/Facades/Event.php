<?php

namespace Support\Facades;

use Support\Facades\Facade;


/**
 * @see \Events\Dispatcher
 */
class Event extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'events'; }

}
