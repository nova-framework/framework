<?php

namespace Shared\Support\Facades;

use Nova\Support\Facades\Facade;


/**
 * @see \Shared\Notifications\ChannelManager
 */
class Notification extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'notifications';
    }
}
