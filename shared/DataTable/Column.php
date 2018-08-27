<?php

namespace Shared\DataTable;


class Column implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $attributes = array();


    /**
     * Create a new Column instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Get an attribute from the column.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : $default;
    }

    /**
     * Set an attribute from the column.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Forget the value of a given attribute.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function forget($key)
    {
        unset($this->attributes[$key]);
    }

    /**
     * Get all of the current attributes on the column.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->forget($offset);
    }

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, array $parameters)
    {
        if (empty($parameters)) {
            return $this->get($method);
        }

        // The method call is for a setter.
        else if (in_array($method, array('orderable', 'searchable', 'className'))) {
            $this->set($method, head($parameters));
        }

        return $this;
    }
}
