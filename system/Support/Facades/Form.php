<?php

namespace Support\Facades;

use Support\Facades\Facade;


/**
 * @see \Html\FormBuilder
 */
class Form extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'form'; }

}
