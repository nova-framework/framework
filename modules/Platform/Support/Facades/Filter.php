<?php

namespace Modules\Platform\Support\Facades;

use Nova\Support\Facades\Facade;


class Filter extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Modules\Platform\Support\Filter';
    }
}
