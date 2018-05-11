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
     * @var array
     */
    protected $types = array();


    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function make($type)
    {
        if (isset($this->types[$type])) {
            return $this->types[$type];
        }

        throw new InvalidArgumentException('Invalid Post type specified');
    }

    public function register($type, array $options = array())
    {
        $this->types[$type] = new PostType($type, $options);
    }

    public function forget($type)
    {
        unset($this->types[$type]);
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

    public function getModel($type)
    {
        if (isset($this->types[$type])) {
            $postType = $this->types[$type];

            return $postType->model();
        }
    }
}
