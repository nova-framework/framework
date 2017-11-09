<?php

namespace App\Modules\Fields\Types;

use App\Modules\Fields\Types\Type as BaseType;


class ArrayType extends BaseType
{
    /**
     * Parse & return the meta item value.
     *
     * @return array
     */
    public function get()
    {
        return unserialize(parent::get());
    }

    /**
     * Parse & set the meta item value.
     *
     * @param array $value
     */
    public function set($value)
    {
        $array = (array) $value;

        parent::set(serialize($array));
    }

    /**
     * Assertain whether we can handle the
     * Field of variable passed.
     *
     * @param  mixed  $value
     * @return bool
     */
    public function isType($value)
    {
        return is_array($value);
    }
}
