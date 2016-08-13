<?php
/**
 * View - load template pages
 *
 * @author David Carr - dave@novaframework.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\Template;
use View\Factory;


class View
{
    /**
     * @var \View\Factory
     */
    private static $factory;

    /**
     * @var array Array of legacy View instances
     */
    private static $items = array();

    /**
     * @var array Array of legacy HTTP headers
     */
    private static $headers = array();


    /**
     * Return a View Factory instance
     *
     * @return \View\Factory
     */
    public static function getFactory()
    {
        if (! isset(static::$factory)) {
            static::$factory = new Factory();
        }

        return static::$factory;
    }

    //--------------------------------------------------------------------
    // Legacy API Methods
    //--------------------------------------------------------------------

    /**
     * Return the stored (legacy) instances.
     *
     * @return array
     */
    public static function getItems()
    {
        return static::$items;
    }

    /**
     * Return the legacy Headers.
     *
     * @return array
     */
    public static function getHeaders()
    {
        return static::$headers;
    }

    /**
     * Add the HTTP header(s) to the legacy Headers array.
     *
     * @param  string  $header HTTP header text
     */
    protected static function addLegacyHeaders($headers)
    {
        if(! is_array($headers)) $headers = array($headers);

        foreach ($headers as $header) {
            list($key, $value) = explode(' ', $header, 1);

            $key = ltrim($key, ':');

            static::$headers[$key] = trim($value);
        }
    }

    /**
     * Magic Method for handling dynamic functions.
     *
     * @param  string  $method
     * @param  array   $params
     * @return void|mixed
     */
    public static function __callStatic($method, $params)
    {
        // Get the View Factory instance;
        $factory = static::getFactory();

        // Process the required action.
        $view = null;

        switch ($method) {
            case 'addHeader':
            case 'addHeaders':
                // Add the Header(s) using the legacy method.
                return call_user_func_array(array(static::class, 'addLegacyHeaders'), $params);

            case 'sendHeaders':
                // No Headers will be sent from there; just go out.
                return null;

            case 'fetch':
            case 'render':
                // Create a standard View instance.
                $view = call_user_func_array(array($factory, 'make'), $params);

                break;
            case 'renderTemplate':
                // Create a Template View instance.
                $view = call_user_func_array(array(Template::class, 'make'), $params);

                break;
            default:
                // Call the non-static method from the View Factory instance.
                return call_user_func_array(array($factory, $method), $params);
        }

        // We would arrive there only for the methods: 'fetch', 'render' and 'renderTemplate'

        if ($method == 'fetch') {
            // Render the object and return the captured output.
            return $view->render();
        }

        // Push the View instance to the legacy items.
        array_push(static::$items, $view);
    }

}
