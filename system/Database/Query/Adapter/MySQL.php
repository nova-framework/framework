<?php
/**
 * MySQL Adapter.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 22th, 2016
 *
 * Based on Pixie Query Builder: https://github.com/usmanhalalit/pixie
 */

namespace Nova\Database\Query\Adapter;

use Nova\Database\Query\Adapter as BaseAdapter;


class MySQL extends BaseAdapter
{
    /**
     * @var string
     */
    protected $sanitizer = '`';
}
