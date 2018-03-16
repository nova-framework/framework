<?php

namespace Modules\Users\Models;

use Nova\Database\ORM\Collection as BaseCollection;
use Nova\Support\Arr;


class FieldCollection extends BaseCollection
{

    /**
     * Find a model in the collection by specified atttribute.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return \Modules\Users\Models\Field
     */
    public function findBy($attribute, $value)
    {
        return Arr::first($this->items, function($key, $model) use ($attribute, $value)
        {
            return $model->getAttribute($attribute) == $value;
        });
    }

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
