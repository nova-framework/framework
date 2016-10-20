<?php

namespace Nova\Support\Facades;

use Nova\Support\Facades\Facade;


/**
 * @see \Nova\Hashing\BcryptHasher
 */
class Hash extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'hash'; }

}
