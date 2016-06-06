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
use Session\FileSessionHandler;
use Session\Store as SessionStore;
use Support\Facades\Cookie;
use Support\MessageBag;


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

        $name = $config['cookie'];

        // Get the Session ID from Cookie, fallback to null.
        $id = Cookie::get($name);

        static::$sessionStore = $session = new SessionStore($name, static::$sessionHandler, $id);

        $session->start();

        return $session;
    }

    /**
     * Intialize a Session Store instance
     *
     * @return void
     */
    public static function init()
    {
        // Load the configuration.
        $config = Config::get('session');

        $savePath = $config['files'];

        $lifeTime = $config['lifetime'] * 60; // This option is in minutes.

        // Get a Session Handler instance.
        $className = $config['handler'];

        static::$sessionHandler = new $className($savePath);

        //
        //ini_set('session.save_handler', 'files');

        session_set_save_handler(static::$sessionHandler, true);

        // The following prevents unexpected effects when using objects as save handlers
        register_shutdown_function('session_write_close');

        // Start the Session.
        session_set_cookie_params($lifeTime, $config['path'], $config['domain']);

        session_start();

        // Create and queue a Cookie containing the proper Session's lifetime.
        $cookie = Cookie::make(
            session_name(),
            session_id(),
            $config['lifetime'],
            $config['path'],
            $config['domain'],
            $config['secure'],
            false
        );

        Cookie::queue($cookie);
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
     * Flash a array containing a message to the session.
     *
     * @param string $message
     * @param string $type
     *
     * @return void
     */
    public static function pushStatus($message, $type = 'success')
    {
        $instance = static::getSessionStore();

        $status = array('type' => $type, 'text' => $message);

        // Push the status on Session.
        $instance->push('status', $status);
    }

    /**
     * Display the one time Messages, then clear them from the Session.
     *
     * @param  string $name default Session name
     *
     * @return string
     */
    public static function getMessages()
    {
        $instance = static::getSessionStore();

        if (! $instance->has('status')) {
            return null;
        }

        // Pull the Message from the Session Store.
        $messages = $instance->remove('status');

        //
        $content = null;

        foreach ($messages as $message) {
            $content .= static::createMessage($message);
        }

        return $content;
    }

    /**
     * Display a one time Message, then clear it from the Session.
     *
     * @param  string $name default Session name
     *
     * @return string
     */
    public static function message($name = null)
    {
        $instance = static::getSessionStore();

        if(is_null($name)) {
            foreach (array('info', 'success', 'warning', 'danger') as $key) {
                if ($instance->has($key)) {
                    $name = $key;

                    break;
                }
            }
        }

        if (! is_null($name) && $instance->has($name)) {
            // Pull the Message from the Session Store.
            $message = $instance->remove($name);

            return static::createMessage($message, $name);
        }
    }

    protected static function createMessage($message, $name = null)
    {
        if(is_array($message)) {
            $type    = $message['type'];
            $message = $message['text'];
        } else {
            $type = $name;
        }

        // Adjust the alert Type.
        switch ($type) {
            case 'info':
            case 'success':
            case 'warning':
            case 'danger':
                break;

            default:
                $type = 'success';

                break;
        }

        // Handle the multiple line messages.
        if($message instanceof MessageBag) {
            $message = $message->all();
        }

        // Handle the array messages.
        if (is_array($message)) {
            if (count($message) > 1) {
                $message = '<ul><li>' .implode('</li><li>', $message) .'</li></ul>';
                        } else if(! empty($message)) {
                $message = array_shift($message);
            } else {
                // An empty array?
                $message = '';
            }
        }

        // Fetch the associated Template Fragment and return the result.
        return Template::make('message', compact('type', 'message'), TEMPLATE)->fetch();
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

        // Call the non-static method from the Session Store instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
