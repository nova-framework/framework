<?php
/**
 * View - load template pages
 *
 * @author David Carr - dave@novaframework.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Support\Facades\Facade;


class View extends Facade
{
    /**
     * @var array Array of legacy View instances
     */
    private static $items = array();

    /**
     * @var array Array of legacy HTTP headers
     */
    private static $headers = array();


    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'view'; }

    /**
     * Magic Method for handling dynamic functions.
     *
     * @param  string  $method
     * @param  array   $params
     * @return void|mixed
     */
    public static function __callStatic($method, $params)
    {
        switch ($method) {
            case 'addHeader':
            case 'addHeaders':
                // Add the Header(s) using the legacy method.
                return call_user_func_array(array(static::class, 'addLegacyHeaders'), $params);

            case 'sendHeaders':
                // No Headers will be sent from there.
                return null;

            default:
                break;
        }

        // Get the associated instance.
        $accessor = static::getFacadeAccessor();

        $instance = static::resolveFacadeInstance($accessor);

        // Process the required action.
        $view = null;

        switch ($method) {
            case 'render':
                // Create a standard View instance.
                $view = call_user_func_array(array($instance, 'make'), $params);

                break;
            case 'renderTemplate':
                // Create a Template View instance.
                $factory = static::resolveFacadeInstance('template');

                $view = call_user_func_array(array($factory, 'make'), $params);

                break;
            default:
                // Call the non-static method from the View Factory instance.
                return call_user_func_array(array($instance, $method), $params);
        }

        //
        // We can arrive there only for the methods: 'render' and 'renderTemplate'

        array_push(static::$items, $view);
    }

    //--------------------------------------------------------------------
    // Legacy API Methods
    //--------------------------------------------------------------------

    /**
     * Return the stored (legacy) View instances.
     *
     * @return array
     */
    public static function getItems()
    {
        return static::$items;
    }

    /**
     * Return the stored (legacy) Headers.
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

}
