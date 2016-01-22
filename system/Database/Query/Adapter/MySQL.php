<?php

namespace Nova\Database\Query\Adapter;

use Nova\Database\Query\Adapter as BaseAdapter;


class MySQL extends BaseAdapter
{
    /**
     * @var string
     */
    protected $sanitizer = '`';
}
