<?php

namespace Support\Facades;

use Support\Facades\Facade;


/**
 * @see \Auth\AuthManager
 * @see \Auth\Guard
 */
class Auth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'auth'; }
}
