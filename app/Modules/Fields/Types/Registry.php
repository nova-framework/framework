<?php

namespace App\Modules\Fields\Types;

use App\Modules\Fields\Types\Type;

use ArrayAccess;
use InvalidArgumentException;


class Registry implements ArrayAccess
{
    protected $registered = array();


    public function instance()
    {
        return $this;
    }

    public function register($mixed)
    {
        if ($mixed instanceof Type) {
            $this->registerClass(get_class($mixed), $mixed);

            return true;
        } else if (is_array($mixed)) {
            foreach ($mixed as $type) {
                $this->register($type);
            }

            return true;
        }

        throw new InvalidArgumentException('The register() input must either be a Type or array of Type.');
    }

    protected function registerClass($class, $instance)
    {
        if (! isset($this->registered[$class])) {
            $this->registered[$class] = $instance;

            return true;
        }

        throw new InvalidArgumentException("The Type is already registered. [$class]");
    }

    public function findTypeFor($value)
    {
        $types = array_reverse($this->registered);

        foreach ($types as $type) {
            if ($type->isType($value)) {
                return $type;
            }
        }

        $type = gettype($value);

        throw new InvalidArgumentException("There is no Type registered for the variable Type. [$type].");
    }

    public function registered()
    {
        return $this->registered;
    }

    public function get($type)
    {
        if (isset($this->registered[$type])) {
            return $this->registered[$type];
        }

        throw new InvalidArgumentException("There is no Type registered. [$type].");
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
