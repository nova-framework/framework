<?php

namespace Modules\Content\Platform;

use Nova\Container\Container;
use Nova\Support\Arr;

use Modules\Content\Models\MenuItem;

use Closure;
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

        //
        MenuItem::forgetInstanceRelation($type);
    }

    public function make($type)
    {
        if (isset($this->types[$type])) {
            return $this->types[$type];
        }

        throw new InvalidArgumentException("The Content type [{$type}] is not registered");
    }

    public function all()
    {
        return array_values($this->types);
    }

    public function get(Closure $callback = null)
    {
        if (is_null($callback)) {
            // By default we will filter out all the hidden types.

            $callback = function ($type)
            {
                return ! $type->isHidden();
            };
        }

        return array_filter($this->all(), $callback);
    }

    public function getNames()
    {
        return array_map(function ($type)
        {
            return $type->name();

        }, $this->get());
    }

    public function findModelByType($type)
    {
        try {
            $instance = $this->make($type);

            return $instance->model();
        }
        catch (InvalidArgumentException $e) {
            // Nothing to do.
        }
    }

    public function findBySlug($slug, $default = null, $plural = true)
    {
        return Arr::first($this->types, function ($name, $type) use ($slug, $plural)
        {
            return ($slug === $type->slug($plural));

        }, $default);
    }

    public function routePattern($plural = true)
    {
        $types = $this->getRouteSlugs($plural);

        return '(' .implode('|', $types) .')';
    }

    public function getRouteSlugs($plural = true)
    {
        return array_map(function ($type) use ($plural)
        {
            return $type->slug($plural);

        }, $this->get());
    }

    public function getCurrentLocale()
    {
        $languageManager = $this->container['language'];

        return $languageManager->getLocale();
    }
}
