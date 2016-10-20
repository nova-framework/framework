<?php

namespace Nova\Support\Facades;

use Nova\Support\Facades\Facade;


/**
 * @see \Nova\Database\DatabaseManager
 * @see \Nova\Database\Connection
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
