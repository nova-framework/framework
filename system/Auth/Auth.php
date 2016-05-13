<?php
/**
 * Auth - A simple Auth Facade.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Auth;

use Core\Config;

use \Closure;


class Auth
{
    /**
     * The currently active Authentication Guards.
     *
     * @var array
     */
     public static $guards = array();

    /**
     * The third-party Guard Registrar.
     *
     * @var array
     */
    public static $registrar = array();

    /**
     * Get an Authentication Guard instance.
     *
     * @param  string  $guard
     * @return Guard
     */
    public static function guard($guard = null)
    {
        $guard = ($guard !== null) ? $guard : 'default';

        if ( ! isset(static::$guards[$guard])) {
            static::$guards[$guard] = static::factory($guard);
        }

        return static::$guards[$guard];
    }

    /**
     * Create a new Authentication Guard instance.
     *
     * @param  string  $guard
     * @return Guard
     */
    protected static function factory($guard)
    {
        $config = Config::get('authentication');

        if (isset(static::$registrar[$guard])) {
            $resolver = static::$registrar[$guard];

            return call_user_func($resolver, $config);
        } else if($guard == 'default') {
            return new \Auth\Guard($config);
        }

        throw new \Exception("Auth Guard {$guard} is not supported.");
    }

    /**
     * Register a third-party Authentication Guard.
     *
     * @param  string   $guard
     * @param  Closure  $resolver
     * @return void
     */
    public static function extend($guard, Closure $resolver)
    {
        static::$registrar[$guard] = $resolver;
    }

    /**
     * Magic Method for calling the methods on the default cache Guard.
     *
     * <code>
     *      // Call the "user" method on the default Auth Guard
     *      $user = Auth::user();
     *
     *      // Call the "check" method on the default Auth Guard
     *      Auth::check();
     * </code>
     *
     * @param  string $method
     * @param  array  $params
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        return call_user_func_array(array(static::guard(), $method), $params);
    }
}
