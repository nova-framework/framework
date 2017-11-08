<?php

namespace App\Modules\Fields\Fields;

use App\Modules\Fields\Fields\Field;


class StringField extends Field
{
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
    public function isField($value)
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
