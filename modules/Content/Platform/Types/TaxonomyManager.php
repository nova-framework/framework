<?php

namespace Modules\Content\Platform\Types;

use Nova\Container\Container;

use Modules\Content\Platform\ContentTypeManager;
use Modules\Content\Platform\Types\Taxonomy;

use InvalidArgumentException;


class TaxonomyManager extends ContentTypeManager
{

    public function register($className, array $options = array())
    {
        if (! is_subclass_of($className, $baseClass = Taxonomy::class)) {
            throw new InvalidArgumentException("The Taxonomy Type class must be a subclass of [{$baseClass}]");
        }

        $taxonomyType = new $className($this, $options);

        //
        $type = $taxonomyType->name();

        if (isset($this->types[$type])) {
            throw new InvalidArgumentException("The Taxonomy type [{$type}] is already registered");
        }

        $this->types[$type] = $taxonomyType;

        return $this;
    }
}
