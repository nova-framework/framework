<?php

namespace Modules\Content\Support\Facades;

use Nova\Support\Facades\Facade;

use Modules\Content\Platform\ContentLabelManager;


class ContentLabel extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ContentLabelManager::class;
    }
}
