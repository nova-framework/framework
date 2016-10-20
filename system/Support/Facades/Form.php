<?php

namespace Nova\Support\Facades;

use Nova\Support\Facades\Facade;


/**
 * @see \Nova\Html\FormBuilder
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
