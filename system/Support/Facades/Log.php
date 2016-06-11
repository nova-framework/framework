<?php

namespace Support\Facades;

/**
 * @see \Log\Writer
 */
class Log extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'log'; }

}
