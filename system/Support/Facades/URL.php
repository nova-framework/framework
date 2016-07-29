<?php

namespace Support\Facades;

use Support\Facades\Facade;


/**
 * @see \Routing\UrlGenerator
 */
class URL extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'url'; }

}
