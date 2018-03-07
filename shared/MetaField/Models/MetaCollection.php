<?php

namespace Shared\MetaField\Models;

use Nova\Database\ORM\Collection as BaseCollection;


class MetaCollection extends BaseCollection
{

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        $item = $this->where('key', $key)->first();

        if (! is_null($item)) {
            return $item->value;
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return ! is_null($this->__get($key));
    }
}
