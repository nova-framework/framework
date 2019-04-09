<?php

namespace Modules\Content\Platform;

use Nova\Container\Container;

use Modules\Content\Platform\Types\Post;

use InvalidArgumentException;


class PostManager
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

        throw new InvalidArgumentException('Invalid Post type specified');
    }

    public function register($className, array $options = array())
    {
        if (! is_subclass_of($className, $baseClass = Post::class)) {
            throw new InvalidArgumentException("The Post Type class must be a subclass of [{$baseClass}]");
        }

        $postType = new $className($this, $options);

        //
        $type = $postType->name();

        if (isset($this->types[$type])) {
            throw new InvalidArgumentException("The Post type [{$type}] is already registered");
        }

        $this->types[$type] = $postType;
    }

    public function forget($type)
    {
        unset($this->types[$type]);
    }

    public function getTypes()
    {
        return array_values($this->types);
    }

    public function getRouteSlugs($plural = false)
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

    public function getModel($type)
    {
        if (isset($this->types[$type])) {
            $postType = $this->types[$type];

            return $postType->model();
        }

        throw new InvalidArgumentException('Invalid Post type specified');
    }

    public function getCurrentLocale()
    {
        $languageManager = $this->container['language'];

        return $languageManager->getLocale();
    }
}
