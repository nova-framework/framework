<?php
/**
 * Language - A Facade to the Language.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Language\Language as CoreLanguage;

use Support\Facades\Facade;
use Support\Facades\Cookie;
use Support\Facades\Session;

use ReflectionMethod;
use ReflectionException;


class Language extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'language'; }
    
}
