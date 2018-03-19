<?php

namespace Modules\Content\Support\Facades;

use Nova\Support\Facades\Facade;

use Modules\Content\Support\Action as Dispatcher;


class Action extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Dispatcher::class;
    }
}
