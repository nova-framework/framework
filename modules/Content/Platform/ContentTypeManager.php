<?php

namespace Modules\Content\Platform;

use Nova\Container\Container;

use InvalidArgumentException;


abstract class ContentTypeManager
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

    public function make($type)
    {
        if (isset($this->types[$type])) {
            return $this->types[$type];
        }

        throw new InvalidArgumentException("The Content type [{$type}] is not registered");
    }

    public function getTypes()
    {
        return array_values($this->types);
    }

    public function getModelByType($type)
    {
        try {
            $instance = $this->make($type);

            return $instance->model();
        }
        catch (InvalidArgumentException $e) {
            // Nothing to do.
        }
    }

    public function getRouteSlugs($plural = true)
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
