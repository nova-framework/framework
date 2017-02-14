<?php

namespace Plugins\Widgets\Facades;

use Nova\Support\Facades\Facade;


class Widget extends Facade
{
    /**
     * Get the registered name of the widget.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'widgets';
    }
}
