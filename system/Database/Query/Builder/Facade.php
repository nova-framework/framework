<?php
/**
 * QueryBuilder Facade.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 22th, 2016
 */

namespace Nova\Database\Query\Builder;

use Nova\Database\Query\Builder as QueryBuilder;


/**
 * This class gives the ability to access the Query Builder's non-static methods statically
 *
 * Class QueryBuilder
 *
 */
class Facade
{

    /**
     * @var \Nova\Database\Query\Builder
     */
    protected static $builderInstance;

    /**
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        if (!static::$builderInstance) {
            static::$builderInstance = new QueryBuilder();
        }

        // Call the non-static method from the class instance
        return call_user_func_array(array(static::$builderInstance, $method), $args);
    }

    /**
     * @param \Nova\Database\Query\Builder $queryBuilder
     */
    public static function setInstance(QueryBuilder $queryBuilder)
    {
        static::$builderInstance = $queryBuilder;
    }

}
