<?php

namespace Modules\Content\Platform;

use Nova\Container\Container;

use Modules\Content\Platform\PostType;

use InvalidArgumentException;


class PostTypeManager
{
    /**
     * @var \Nova\Container\Container
     */
    protected $container;

    /**
     * @var \Modules\Content\Platform\PostType[]
     */
    protected $types = array();


    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function make($name)
    {
        if (isset($this->types[$name])) {
            return $this->types[$name];
        }

        throw new InvalidArgumentException('Invalid Post type specified');
    }

    public function register($name, array $options = array())
    {
        $this->types[$name] = new PostType($name, $options);
    }

    public function forgetType($name)
    {
        unset($this->types[$name]);
    }

    public function getTypes()
    {
        return $this->types;
    }

    public function getNames()
    {
        return array_map(function ($type)
        {
            return $type->name();

        }, $this->types);
    }

    public function getSlugs()
    {
        return array_map(function ($type)
        {
            return $type->slug();

        }, $this->types);
    }

    public function getTypeModel($name)
    {
        if (isset($this->types[$name])) {
            $type = $this->types[$name];

            return $type->model();
        }
    }
}
