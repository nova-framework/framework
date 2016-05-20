<?php
/**
 * Language - A Facade to the Language.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Core\Language as CoreLanguage;


class Language
{
    /**
     * Magic Method for calling the methods on the default Language instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        // First handle the static Methods from Core\Language.
        switch ($method) {
            case 'init':
            case 'getInstance':
                return call_user_func_array(array(CoreLanguage::class, $method), $params);

            default:
                break;
        }

        // Get a Core\Language instance.
        $instance = CoreLanguage::getInstance();

        // Call the non-static method from the Language instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
