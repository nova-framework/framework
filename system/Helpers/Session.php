<?php
/**
 * Session Class.
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */

namespace Helpers;

use Core\Template;


/**
 * Prefix sessions with useful methods.
 */
class Session
{
    /**
     * Determine if a session has started.
     *
     * @var boolean
     */
    private static $sessionStarted = false;

    /**
     * if the session have not been started, start the sessions.
     */
    public static function init()
    {
        if (self::$sessionStarted == false) {
            session_start();
            self::$sessionStarted = true;
        }
    }

    /**
     * Determine if a key exists in the session.
     */
    public static function exists($key)
    {
        return isset($_SESSION[SESSION_PREFIX .$key]);
    }

    /**
     * Add a value to a session.
     *
     * @param string $key   name the data to save
     * @param string $value the data to save
     */
    public static function set($key, $value = false)
    {
        /**
        * Check whether a session is set in an array or not.
        * If it is an array, then set all session key-values in a foreach loop.
        */
        if (is_array($key) && $value === false) {
            foreach ($key as $name => $value) {
                $_SESSION[SESSION_PREFIX.$name] = $value;
            }
        } else {
            $_SESSION[SESSION_PREFIX.$key] = $value;
        }
    }

    /**
     * Extract an item from the session, then delete it from the session and finally return the item.
     *
     * @param  string $key item to extract
     *
     * @return mixed|null      return item or null when key does not exists
     */
    public static function pull($key)
    {
        if (isset($_SESSION[SESSION_PREFIX.$key])) {
            $value = $_SESSION[SESSION_PREFIX.$key];
            unset($_SESSION[SESSION_PREFIX.$key]);
            return $value;
        }
        return null;
    }

    /**
     * Get an item from the session.
     *
     * @param  string  $key       item to look for in session
     * @param  boolean $secondkey if used then use as a second key
     *
     * @return mixed|null         returns the key value, or null if the key doesn't exist
     */
    public static function get($key, $secondkey = false)
    {
        if ($secondkey == true) {
            if (isset($_SESSION[SESSION_PREFIX.$key][$secondkey])) {
                return $_SESSION[SESSION_PREFIX.$key][$secondkey];
            }
        } else {
            if (isset($_SESSION[SESSION_PREFIX.$key])) {
                return $_SESSION[SESSION_PREFIX.$key];
            }
        }
        return null;
    }

    /**
     * id
     *
     * @return string with the session id.
     */
    public static function id()
    {
        return session_id();
    }

    /**
     * Regenerate the session_id.
     *
     * @return string session_id
     */
    public static function regenerate()
    {
        session_regenerate_id(true);
        return session_id();
    }

    /**
     * Return the session array.
     *
     * @return array of session indexes
     */
    public static function display()
    {
        return $_SESSION;
    }


    /**
     * Empty and destroy the session.
     *
     * @param  string $key - session name to destroy
     * @param  boolean $prefix - if set to true clear all sessions for the current SESSION_PREFIX
     *
     */
    public static function destroy($key = '', $prefix = false)
    {
        // Only run if the session has started.
        if (self::$sessionStarted == true) {
            // If the key is empty and the $prefix is false.
            if ($key =='' && $prefix == false) {
                session_unset();
                session_destroy();
            } elseif ($prefix == true) {
                // Clear all the session for set SESSION_PREFIX
                foreach ($_SESSION as $key => $value) {
                    if (strpos($key, SESSION_PREFIX) === 0) {
                        unset($_SESSION[$key]);
                    }
                }
            } else {
                // Clear the specified session key.
                unset($_SESSION[SESSION_PREFIX.$key]);
            }
        }
    }

    /**
      * @return string return the message inside div
     */

    /**
     * Display a one time Message, then clear it from the Session.
     *
     * @param  string $name default Session name
     *
     * @return string
     */
    public static function message($name = 'success')
    {
        if (! Session::exists($name)) {
            return null;
        }

        // Pull the Message from Session.
        $message = Session::pull($name);

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
}
