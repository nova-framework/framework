<?php

namespace Support\Facades;

use Support\Facades\Facade;


/**
 * @see \Mail\Mailer
 */
class Mailer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'mailer'; }

}
