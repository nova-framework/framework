<?php

namespace Modules\Content\Platform;

use Nova\Container\Container;

use Modules\Content\Models\MenuItem;
use Modules\Content\Platform\ContentTypeManager;
use Modules\Content\Platform\Types\Post;

use InvalidArgumentException;


class PostTypeManager extends ContentTypeManager
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

        if ($postType->showInNavMenus()) {
            MenuItem::registerInstanceRelation($type, $postType->model());
        }
    }
}
