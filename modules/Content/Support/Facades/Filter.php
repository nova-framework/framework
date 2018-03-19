<?php

namespace Modules\Content\Support\Facades;

use Nova\Support\Facades\Facade;

use Modules\Content\Support\Filter as Dispatcher;


class Filter extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Dispatcher::class;
    }
}
