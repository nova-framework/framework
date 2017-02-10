<?php

namespace Plugins\Cron\Facades;

use Nova\Support\Facades\Facade;


/**
 * @see \Nova\Cron\CronManager
 */
class Cron extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'cron'; }

}
