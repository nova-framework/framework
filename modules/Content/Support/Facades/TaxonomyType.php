<?php

namespace Modules\Content\Support\Facades;

use Nova\Support\Facades\Facade;

use Modules\Content\Platform\TaxonomyTypeManager;


class TaxonomyType extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return TaxonomyTypeManager::class;
    }
}
