<?php

namespace Modules\Content\Platform;

use Nova\Container\Container;


class ContentManager
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

    abstract public function register($className, array $options = array());

    public function forget($type)
    {
        unset($this->types[$type]);
    }

    public function get($type)
    {
        if (isset($this->types[$type])) {
            return $this->types[$type];
        }
    }

    public function getTypes()
    {
        return array_values($this->types);
    }

    public function getModelByType($type)
    {
        if (! is_null($instance = $this->get($type))) {
            return $instance->model();
        }
    }

    public function getRouteSlugs($plural = false)
    {
        $result = array();

        foreach ($this->types as $name => $type) {
            if (! $type->isHidden()) {
                $result[] = $type->slug($plural);
            }
        }

        return $result;
    }

    public function getCurrentLocale()
    {
        $languageManager = $this->container['language'];

        return $languageManager->getLocale();
    }
}
