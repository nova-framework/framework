<?php

namespace App\Modules\Fields\Types;

use App\Modules\Fields\Types\Type as BaseType;


class StringType extends BaseType
{
    /**
     * The type handled by this Type class.
     *
     * @var string
     */
    protected $type = 'string';


    /**
     * Parse & set the meta item value.
     *
     * @param string $value
     */
    public function set($value)
    {
        parent::set((string) $value);
    }

    /**
     * Assertain whether we can handle the Field of variable passed.
     *
     * @param  mixed  $value
     * @return bool
     */
    public function isType($value)
    {
        return is_string($value);
    }

    /**
     * Output value to string.
     *
     * @return string
     */
    public function toString()
    {
        return $this->get();
    }
}
