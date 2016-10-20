<?php

namespace Nova\Support\Facades;

use Nova\Support\Facades\Facade;


/**
 * @see \Nova\Cache\CacheManager
 * @see \Nova\Cache\Repository
 */
class Cache extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'cache'; }

}
