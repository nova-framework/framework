<?php
/**
 * Auth - A simple Auth Facade.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Database\Connection;
use Auth\DatabaseUserProvider;
use Auth\ExtendedUserProvider;
use Auth\Guard as AuthGuard;
use Support\Facades\Cookie;
use Support\Facades\Config;
use Support\Facades\Request;
use Support\Facades\Session;

use Closure;


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
        $guard = $guard ?: 'default';

        if (isset(static::$guards[$guard])) {
            return static::$guards[$guard];
        }

        return static::$guards[$guard] = static::factory($guard);
    }

    /**
     * Create a proper Auth User Provider instance.
     *
     * @param array $config
     * @return \Auth\UserProviderInterface
     *
     * @throw \InvalidArgumentException
     */
    protected static function getUserProvider(array $config)
    {
        // Get the current Authentication Driver.
        $driver = $config['driver'];

        if ($driver == 'database') {
            $table = $config['table'];

            // Get a Database Connection instance.
            $connection = Connection::getInstance();

            return new DatabaseUserProvider($connection, $table);
        } else if ($driver == 'extended') {
            $model = '\\'.ltrim($config['model'], '\\');

            if(! class_exists($model)) {
                throw new \InvalidArgumentException('Invalid Auth Model.');
            }

            return new ExtendedUserProvider($model);
        }

        throw new \InvalidArgumentException('Invalid Auth Driver.');
    }

    /**
     * Create a new Authentication Guard instance.
     *
     * @param  string  $guard
     * @return Guard
     */
    protected static function factory($guard)
    {
        $config = Config::get('auth');

        if (isset(static::$registrar[$guard])) {
            $resolver = static::$registrar[$guard];

            return call_user_func($resolver, $config);
        } else if($guard == 'default') {
            $provider = static::getUserProvider($config);

            // Get the CookieJar instance.
            $cookie = Cookie::instance();

            // Get the Session instance.
            $session = Session::instance();

            // Get the Request instance.
            $request = Request::instance();

            // Get the Auth Guard instance.
            $guard = new AuthGuard($provider, $session, $request);

            // Set the CookieJar instance.
            $guard->setCookieJar($cookie);

            return $guard;
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
