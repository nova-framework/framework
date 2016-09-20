<?php

namespace Support\Facades;

use Support\Facades\Facade;


/**
 * @see \Module\ModuleManager
 */
class Module extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'modules'; }
}
