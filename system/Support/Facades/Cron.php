<?php

namespace Support\Facades;

use Support\Facades\Facade;


/**
 * @see \App\Modules\Cron\Services\Manager
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
