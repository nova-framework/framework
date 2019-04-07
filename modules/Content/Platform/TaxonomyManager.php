<?php

namespace Modules\Content\Platform;

use Nova\Container\Container;

use Modules\Content\Platform\Types\Taxonomy;

use InvalidArgumentException;


class TaxonomyManager
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

    public function get($type)
    {
        if (isset($this->types[$type])) {
            return $this->types[$type];
        }

        throw new InvalidArgumentException('Invalid Taxonomy type specified');
    }

    public function register($className, array $options = array())
    {
        if (! is_subclass_of($className, $baseClass = Taxonomy::class)) {
            throw new InvalidArgumentException("The Taxonomy Type class must be a subclass of [{$baseClass}]");
        }

        $taxonomyType = new $className($this->container, $options);

        //
        $type = $taxonomyType->name();

        if (isset($this->types[$type])) {
            throw new InvalidArgumentException("The Post type [{$type}] is already registered");
        }

        $this->types[$type] = $taxonomyType;
    }

    public function forget($type)
    {
        unset($this->types[$type]);
    }

    public function getTypes()
    {
        return array_values($this->types);
    }

    public function getSlugs($plural = false)
    {
        return array_map(function ($type) use ($plural)
        {
            if (! $plural) {
                return $type->name();
            }

            return $type->slug();

        }, array_filter($this->getTypes(), function ($type)
        {
            return ! $type->isHidden();
        }));
    }

    public function getCurrentLocale()
    {
        $languageManager = $this->container['language'];

        return $languageManager->getLocale();
    }
}
