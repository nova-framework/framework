<?php
/**
 * Session - A Facade to the Session Store.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Core\Config;
use Core\Template;
use Helpers\Cookie as LegacyCookie;

use Session\FileSessionHandler;
use Session\NativeSessionHandler;
use Session\Store as SessionStore;


class Session
{
    /**
     * The Session Store instance being handled.
     *
     * @var \Session\Store|null
     */
    protected static $sessionStore;

    /**
     * The Session Handler instance being handled.
     *
     * @var \Session\FileSessionHandler|null
     */
    protected static $sessionHandler;


    /**
     * Return a Session Store instance
     *
     * @return \Session\Store
     */
    protected static function getSessionStore()
    {
        if (isset(static::$sessionStore)) {
            return static::$sessionStore;
        }

        // Load the configuration.
        $config = Config::get('session');

        $cookie = $config['cookie'];

        if (LegacyCookie::exists($cookie)) {
            $id = LegacyCookie::get($cookie);
        } else {
            $id = null;
        }

        static::$sessionStore = $store = new SessionStore($cookie, static::$sessionHandler, $id);

        if ($id === null) {
            $domain = isset($config['domain']) ? $config['domain'] : false;

            LegacyCookie::set($cookie, $store->getId(), -1, $config['path'], $domain);
        }

        return $store;
    }

    public static function init()
    {
        // Load the configuration.
        $config = Config::get('session');

        $path = $config['files'];

        $lifeTime = $config['lifetime'] * 60; // The option is in minutes.

        // Get a Session Handler instance.
        static::$sessionHandler = $handler = new FileSessionHandler($path, $lifeTime);

        //
        ini_set('session.save_handler', 'files');

        session_set_save_handler($handler, true);

        session_start();

        $instance = static::getSessionStore();

        $instance->start();
    }

    /**
     * Return a Session Store instance
     *
     * @return \Session\Store
     */
    public static function instance()
    {
        return static::getSessionStore();
    }

    /**
     * Display a one time Message, then clear it from the Session.
     *
     * @param  string $name default Session name
     *
     * @return string
     */
    public static function message($name = 'success')
    {
        $instance = static::getSessionStore();

        if (! $instance->has($name)) {
            return null;
        }

        // Get the Message from the Session Store.
        $message = $instance->get($name);

        // Delete the message from the Session Store.
        $instance->forget($name);

        if (is_array($message)) {
            // The Message is structured in the New Style.
            $name    = $message['type'];
            $message = $message['text'];
        }

        // Prepare the allert Type and Icon.
        $type = null;

        switch ($name) {
            case 'info':
                $icon = 'info';
                break;
            case 'warning':
                $icon = 'warning';
                break;
            case 'danger':
                $icon = 'bomb';
                break;
            default:
                $icon = 'check';
                $type = 'success';
        }

        $type = ($type !== null) ? $type : $name;

        // Fetch the associated Template Fragment and return the result.
        return Template::make('message', compact('type', 'icon', 'message'))->fetch();
    }

    /**
     * Magic Method for calling the methods on the default Session Store instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        // Get the Session Store instance.
        $instance = static::getSessionStore();

        // Call the non-static method from the Request instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
