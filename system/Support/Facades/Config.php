<?php

namespace Support\Facades;

use Support\Facades\Facade;


/**
 * @see \Config\Repository
 */
class Config extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'config'; }

}
