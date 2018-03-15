<?php

namespace Modules\Users\Models;

use Nova\Database\ORM\Collection as BaseCollection;


class FieldCollection extends BaseCollection
{

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $item = $this->where('name', $name)->first();

        if (! is_null($item)) {
            return $item->value;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return ! is_null($this->__get($name));
    }
}
