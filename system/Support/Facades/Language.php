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
     * Initialize the Language Service.
     *
     * @return string
     */
    public static function initialize()
    {
        $accessor = static::getFacadeAccessor();

        $instance = static::resolveFacadeInstance($accessor);

        //
        $locale = static::$app['config']['app.locale'];

        if (Session::has('language')) {
            $locale = Session::get('language', $locale);
        } else if(Cookie::has(PREFIX .'language')) {
            $locale = Cookie::get(PREFIX .'language', $locale);

            Session::set('language', $locale);
        }

        $instance->setLocale($locale);
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'language'; }
    
}
