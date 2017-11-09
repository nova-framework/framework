<?php

namespace App\Modules\Fields\Types;

use App\Modules\Fields\Types\Type as BaseType;


class BooleanType extends BaseType
{
    /**
     * The partial View used for editor rendering.
     *
     * @var string
     */
    protected $view = 'Editor/Boolean';


    /**
     * Parse & return the meta item value.
     *
     * @return bool
     */
    public function get()
    {
        return intval(parent::get()) ? true : false;
    }

    /**
     * Parse & set the meta item value.
     *
     * @param bool $value
     */
    public function set($value)
    {
        parent::set(intval($value));
    }

    /**
     * Assertain whether we can handle the Field of variable passed.
     *
     * @param  mixed  $value
     * @return bool
     */
    public function isType($value)
    {
        return is_bool($value);
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

    /**
     * Output value to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
