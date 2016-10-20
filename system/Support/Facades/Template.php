<?php

namespace Nova\Support\Facades;

use Nova\Support\Facades\Facade;

/**
 * @see \Nova\Template\Factory
 * @see \Nova\Template\Template
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
