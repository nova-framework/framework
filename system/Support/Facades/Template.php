<?php

namespace Support\Facades;

use Support\Facades\Facade;

/**
 * @see \Template\Factory
 * @see \View\View
 */
class Template extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'template'; }

}
