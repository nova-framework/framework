<?php
/**
 * Join Builder.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 22th, 2016
 *
 * Based on Pixie Query Builder: https://github.com/usmanhalalit/pixie
 */

namespace Nova\Database\Query\Builder;

use Nova\Database\Query\Builder as BaseBuilder;


class Join extends BaseBuilder
{
    /**
     * @param $key
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function on($key, $operator, $value)
    {
        return $this->joinHandler($key, $operator, $value, 'AND');
    }

    /**
     * @param $key
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orOn($key, $operator, $value)
    {
        return $this->joinHandler($key, $operator, $value, 'OR');
    }

    /**
     * @param        $key
     * @param null   $operator
     * @param null   $value
     * @param string $joiner
     *
     * @return $this
     */
    protected function joinHandler($key, $operator = null, $value = null, $joiner = 'AND')
    {
        $key = $this->addTablePrefix($key);

        $value = $this->addTablePrefix($value);

        $this->statements['criteria'][] = compact('key', 'operator', 'value', 'joiner');

        return $this;
    }
}
