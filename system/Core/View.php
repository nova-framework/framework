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
        // Process first the (legacy) methods associated to Headers.
        switch ($method) {
            case 'addHeader':
            case 'addHeaders':
                // Add the Header(s) using the legacy method.
                return call_user_func_array(array(static::class, 'addLegacyHeaders'), $params);

            case 'sendHeaders':
                // The Headers should not be sent from there; go out.
                return;

            default:
                break;
        }

        // Get the Factory instance.
        if ($method == 'renderTemplate') {
            $accessor = 'template';
        } else {
            $accessor = static::getFacadeAccessor();
        }

        $instance = static::resolveFacadeInstance($accessor);

        //
        // Process the requested method.

        if (! str_starts_with($method, 'render')) {
            // Call the non-static method from the View Factory instance.
            return call_user_func_array(array($instance, $method), $params);
        }

        // Create a View instance calling the Factory.
        $view = call_user_func_array(array($instance, 'make'), $params);

        // Push the View instance to (legacy) items.
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
