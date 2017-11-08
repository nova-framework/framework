<?php

namespace App\Modules\Fields\Support;

use App\Modules\Fields\Fields\Field;

use ArrayAccess;
use InvalidArgumentException;


class FieldRegistry implements ArrayAccess
{
    protected $registered = array();


    public function instance()
    {
        return $this;
    }

    public function register($mixed)
    {
        if ($mixed instanceof Field) {
            $this->registerClass(get_class($mixed), $mixed);

            return true;
        } else if (is_array($mixed)) {
            foreach ($mixed as $field) {
                $this->register($field);
            }

            return true;
        }

        throw new InvalidArgumentException('The register() input must either be a Field or array of Field.');
    }

    protected function registerClass($class, $instance)
    {
        if (! isset($this->registered[$class])) {
            $this->registered[$class] = $instance;

            return true;
        }

        throw new InvalidArgumentException("The Field is already registered. [$class]");
    }

    public function findFieldFor($value)
    {
        $fields = array_reverse($this->registered);

        foreach ($fields as $field) {
            if ($field->isField($value)) {
                return $field;
            }
        }

        $field = gettype($value);

        throw new InvalidArgumentException("There is no Field registered for the variable Field. [$field].");
    }

    public function registered()
    {
        return $this->registered;
    }

    public function get($field)
    {
        if (isset($this->registered[$field])) {
            return $this->registered[$field];
        }

        throw new InvalidArgumentException("There is no Field registered. [$field].");
    }

    public function has($class)
    {
        return array_key_exists($class, $this->registered);
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->registerClass($offset, $value);
    }

    public function offsetUnset($offset)
    {
        unset($this->registered[$offset]);
    }
}
