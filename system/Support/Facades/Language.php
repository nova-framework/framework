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

    public static function initialize()
    {
        $language = static::$app['language'];

        //
        $locale = static::$app['config']['app.locale'];

        if (Session::has('language')) {
            $locale = Session::get('language', $locale);
        } else if(Cookie::has(PREFIX .'language')) {
            $locale = Cookie::get(PREFIX .'language', $locale);

            Session::set('language', $locale);
        }

        $language->setLocale($locale);
    }

    /**
     * Get the language for the Views.
     *
     * @param  string $value this is a "word" value from the language file
     * @param  string $name  name of the file with the language
     * @param  string $code  optional, language code
     *
     * @return string
     */
    public static function show($value, $name, $code = LANGUAGE_CODE)
    {
        $language = static::instance('legacy_api', $code);

        // Load the specified Language file.
        $language->load($name, $code);

        return $language->get($value, $code);
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'language'; }
}
