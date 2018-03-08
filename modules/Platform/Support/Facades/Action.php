<?php

namespace Modules\Platform\Support\Facades;

use Nova\Support\Facades\Facade;


class Action extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Modules\Platform\Support\Action';
    }
}
