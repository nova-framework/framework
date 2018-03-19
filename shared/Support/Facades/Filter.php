<?php

namespace Shared\Support\Facades;

use Nova\Support\Facades\Facade;


class Filter extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Shared\Hooks\Filter';
    }
}
