<?php
/**
 * Request Class
 *
 * @version 3.0
 */

namespace Helpers;

/**
 * It contains the request information and provide methods to fetch request body.
 */
class Request
{
    /**
     * Gets the request method.
     *
     * @return string
     */
    public static function getMethod()
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';

        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        } elseif (isset($_REQUEST['_method'])) {
            $method = $_REQUEST['_method'];
        }

        return strtoupper($method);
    }

    /**
     * Safer and better access to $_POST.
     *
     * @param  string   $key
     * @static static method
     *
     * @return mixed
     */
    public static function post($key)
    {
        return array_key_exists($key, $_POST)? $_POST[$key]: null;
    }

    /**
     * Safer and better access to $_FILES.
     *
     * @param  string   $key
     * @static static method
     *
     * @return mixed
     */
    public static function files($key)
    {
        return array_key_exists($key, $_FILES)? $_FILES[$key]: null;
    }

    /**
     * Safer and better access to $_GET.
     *
     * @param  string   $key
     * @static static method
     *
     * @return mixed
     */
    public static function query($key)
    {
        return self::get($key);
    }

    /**
     * Safer and better access to $_GET.
     *
     * @param  string   $key
     * @static static method
     *
     * @return mixed
     */
    public static function get($key)
    {
        return array_key_exists($key, $_GET)? $_GET[$key]: null;
    }

    /**
     * Detect if request is Ajax.
     *
     * @static static method
     *
     * @return boolean
     */
    public static function isAjax()
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        }
        return false;
    }

    /**
     * Detect if request is POST request.
     *
     * @static static method
     *
     * @return boolean
     */
    public static function isPost()
    {
        return $_SERVER["REQUEST_METHOD"] === "POST";
    }

    /**
     * Detect if request is GET request.
     *
     * @static static method
     *
     * @return boolean
     */
    public static function isGet()
    {
        return $_SERVER["REQUEST_METHOD"] === "GET";
    }
}
