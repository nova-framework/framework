<?php

namespace Shared\Support\Facades;

use Nova\Support\Facades\Facade;


class Action extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Shared\Platform\Action';
    }
}
