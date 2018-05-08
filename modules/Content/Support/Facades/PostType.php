<?php

namespace Modules\Content\Support\Facades;

use Nova\Support\Facades\Facade;

use Modules\Content\Platform\PostTypeManager;


class PostType extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PostTypeManager::class;
    }
}
