<?php
/**
 * Language - A Facade to the Language.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Nova\Support\Facades;

use Nova\Support\Facades\Facade;

/**
 * @see \Nova\Language\Language
 * @see \Nova\Language\LanguageManager
 */
class Language extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'language'; }

}
