<?php

namespace Modules\Content\Support\Facades;

use Nova\Support\Facades\Facade;

use Modules\Content\Platform\Types\PostManager;


class PostType extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PostManager::class;
    }
}
