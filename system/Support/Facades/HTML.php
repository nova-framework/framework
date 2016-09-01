<?php

namespace Support\Facades;

use Support\Facades\Facade;


/**
 * @see \Html\HtmlBuilder
 */
class HTML extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'html'; }

}
