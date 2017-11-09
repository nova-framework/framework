<?php

namespace App\Modules\Fields\Types;

use App\Modules\Fields\Types\Type as BaseType;


class DoubleType extends BaseType
{
    /**
     * The type handled by this Type class.
     *
     * @var string
     */
    protected $type = 'double';


    /**
     * Parse & return the meta item value.
     *
     * @return int
     */
    public function get()
    {
        return doubleval(parent::get());
    }

    /**
     * Parse & set the meta item value.
     *
     * @param int $value
     */
    public function set($value)
    {
        parent::set(doubleval($value));
    }

    /**
     * Ascertain whether we can handle the Field of variable passed.
     *
     * @param  mixed  $value
     * @return bool
     */
    public function isType($value)
    {
        return is_double($value);
    }

    /**
     * Output value to string.
     *
     * @return string
     */
    public function toString()
    {
        return (string) $this->get();
    }
}
