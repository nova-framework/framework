<?php

namespace Support\Facades;

use Support\Facades\Facade;


/**
 * @see \Encryption\Encrypter
 */
class Crypt extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'encrypter'; }

}
