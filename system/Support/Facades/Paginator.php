<?php

namespace Support\Facades;

use Support\Facades\Facade;


/**
 * @see \Pagination\Factory
 */
class Paginator extends Facade
{
    /**
     * Return the Application instance.
     *
     * @return \Pagination\Factory
     */
    public static function instance()
    {
        $accessor = static::getFacadeAccessor();

        return static::resolveFacadeInstance($accessor);
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'paginator'; }

}
