<?php
/**
 * Auth - A simple Auth Facade.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Auth;

use Core\Config;


class Auth
{
    /**
     * The Authentication Guard instance.
     *
     * @var \Auth\Guard
     */
    protected static $guard;


    /**
     * Call the Guard methods dynamically.
     *
     * @param  string $method
     * @param  array  $params
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $config = Config::get('authentication');

        if (! isset(static::$guard)) {
            $className = '\\' .ltrim($config['guard'], '\\');

            static::$guard = new $className();
        }

        return call_user_func_array(array(static::$guard, $method), $params);
    }
}
