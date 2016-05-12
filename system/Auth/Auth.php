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
     * Get the Guard instance dynamically.
     *
     * @return \Auth\Guard
     */
    protected static function getGuardInstance()
    {
        if (! isset(static::$guard)) {
            $config = Config::get('authentication');

            $className = '\\' .ltrim($config['guard'], '\\');

            static::$guard = new $className($config);
        }

        return static::$guard;
    }

    /**
     * Call the Guard methods dynamically.
     *
     * @param  string $method
     * @param  array  $params
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $guard = static::getGuardInstance();

        return call_user_func_array(array($guard, $method), $params);
    }
}
