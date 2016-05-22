<?php
/**
 * Session - A Facade to the Session Store.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Core\Template;
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
     * Return a Session Store instance
     *
     * @return \Session\Store
     */
    protected static function getSessionStore()
    {
        if (isset(static::$sessionStore)) {
            return static::$sessionStore;
        }

        return static::$sessionStore = new SessionStore(PREFIX .'_session');
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
        $instance->delete($name);

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
