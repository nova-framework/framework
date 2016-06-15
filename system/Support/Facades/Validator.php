<?php

namespace Support\Facades;

use Support\Facades\Facade;


/**
 * @see \Validation\Factory
 */
class Validator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'validator'; }

}
