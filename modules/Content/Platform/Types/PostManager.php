<?php

namespace Modules\Content\Platform\Types;

use Nova\Container\Container;

use Modules\Content\Platform\ContentManager;
use Modules\Content\Platform\Types\Post;

use InvalidArgumentException;


class PostManager extends ContentManager
{

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

        return $this;
    }
}
