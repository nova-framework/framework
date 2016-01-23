<?php
/**
 * Nested Criteria.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 22th, 2016
 *
 * Based on Pixie Query Builder: https://github.com/usmanhalalit/pixie
 */

namespace Nova\Database\Query\Builder;

use Nova\Database\Query\Builder as BaseBuilder;


class NestedCriteria extends BaseBuilder
{
    /**
     * @param        $key
     * @param null   $operator
     * @param null   $value
     * @param string $joiner
     *
     * @return $this
     */
    protected function whereHandler($key, $operator = null, $value = null, $joiner = 'AND')
    {
        $key = $this->addTablePrefix($key);

        $this->statements['criteria'][] = compact('key', 'operator', 'value', 'joiner');

        return $this;
    }
}
