<?php
/**
 * View - load template pages
 *
 * @author David Carr - dave@novaframework.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\BaseView;
use Core\Template;

use Response;


/**
 * View class to load views files.
 */
class View extends BaseView
{
    /**
     * @var array Array of legacy BaseView instances
     */
    private static $legacyItems = array();

    /**
     * @var array Array of legacy HTTP headers
     */
    private static $legacyHeaders = array();


    /**
     * Constructor
     * @param mixed $path
     * @param array $data
     *
     * @throws \UnexpectedValueException
     */
    protected function __construct($view, $path, array $data = array())
    {
        parent::__construct($view, $path, $data);
    }

    /**
     * Create a View instance
     *
     * @param string $path
     * @param array|string $data
     * @param string|null $module
     * @return View
     */
    public static function make($view, $data = array(), $module = null)
    {
        list($data, $module) = static::parseParams($data, $module);

        // Prepare the (relative) file path according with Module parameter presence.
        if (is_null($module)) {
            $path = str_replace('/', DS, APPDIR ."Views/$view.php");
        } else {
            $path = str_replace('/', DS, APPDIR ."Modules/$module/Views/$view.php");
        }

        return new View($view, $path, $data);
    }

    //--------------------------------------------------------------------
    // Legacy API Methods
    //--------------------------------------------------------------------

    /**
     * Return the stored (legacy) instances.
     *
     * @return array
     */
    public static function hasLegacyItems()
    {
        return ! empty(static::$legacyItems);
    }

    /**
     * Return the stored (legacy) instances.
     *
     * @return array
     */
    public static function getLegacyItems()
    {
        return static::$legacyItems;
    }

    /**
     * Return the legacy Headers.
     *
     * @return array
     */
    public static function getLegacyHeaders()
    {
        return static::$legacyHeaders;
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

            static::$legacyHeaders[$key] = trim($value);
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
        // Process the legacy Headers management.
        switch ($method) {
            case 'addHeader':
            case 'addHeaders':
                return call_user_func_array(array(static::class, 'addLegacyHeaders'), $params);

            case 'sendHeaders':
                return null;

            default:
                break;
        }

        // The called Class; for getting a View instance.
        $className = static::class;

        // Flag for fetching the View rendering output.
        $shouldFetch = false;

        // Prepare the required information.
        if ($method == 'fetch') {
            $shouldFetch = true;
        } else if ($method == 'render') {
            // Nothing to do; there is no Headers sending.
        } else if ($method == 'renderTemplate') {
            $className = Template::class;
        } else {
            // No valid Compat Method found; go out.
            return null;
        }

        // Create a View instance, using the current Class and the given parameters.
        $instance = call_user_func_array(array($className, 'make'), $params);

        if ($shouldFetch) {
            // Render the object and return the captured output.
            return $instance->fetch();
        }

        array_push(static::$legacyItems, $instance);
    }

}
