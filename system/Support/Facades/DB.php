<?php

namespace Support\Facades;

use Support\Facades\Facade;


/**
 * @see \Database\DatabaseManager
 * @see \Database\Connection
 */
class DB extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'db'; }

}
