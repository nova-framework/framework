<?php

namespace Nova\Database\Query;

use Nova\Database\Query\Builder as QueryBuilder;

/**
 * This class gives the ability to access non-static methods statically
 *
 * Class Facade
 *
 */
class AliasFacade
{

    /**
     * @var \Nova\Database\Query\Builder
     */
    protected static $instance;

    /**
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        if (!static::$instance) {
            static::$instance = new QueryBuilder();
        }

        // Call the non-static method from the class instance
        return call_user_func_array(array(static::$instance, $method), $args);
    }
}
