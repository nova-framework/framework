<?php

namespace App\Modules\Fields\Types;

use App\Modules\Fields\Types\Type as BaseType;


class BooleanType extends BaseType
{
    /**
     * The type handled by this Type class.
     *
     * @var string
     */
    protected $type = 'boolean';

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
        $value = parent::get();

        return intval($value) === 1;
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
}
