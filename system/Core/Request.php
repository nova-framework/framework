<?php
/**
 * Request - Manage the HTTP Requests.
 *
 * @version 3.0
 */

namespace Core;

/**
 * Contains the Request information and provides methods to fetch the HTTP Request body.
 */
class Request
{
    private static $cache = null; // There will be cached the PUT data if exists.

    /**
     * Retrieve the request method.
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

    public static function getIpAddress()
    {
        if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }

        return $ip_address;
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
        return array_key_exists($key, $_POST) ? $_POST[$key] : null;
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
        return array_key_exists($key, $_FILES) ? $_FILES[$key] : null;
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
        return array_key_exists($key, $_GET) ? $_GET[$key] : null;
    }

   /**
     * Safer and better access to PUT parameters.
     *
     * @param  string   $key
     * @static static method
     *
     * @return mixed
     */
    public static function put($key = null)
    {
        if($_SERVER["REQUEST_METHOD"] !== "PUT") {
            // Go out when the Method is not a PUT, avoiding to confuse the input parser.
            return null;
        }

        // The reading of the PUT variables is a one time hit only; use a caching method.
        // To note that the initial Cache's null value is used as an initialization flag.

        if(static::$cache === null) {
            // First, initialize the Cache as a empty array.
            static::$cache = array();

            // Get the PHP's raw input data.
            $input = file_get_contents('php://input', false , null, -1 , $_SERVER['CONTENT_LENGTH']);

            // Parse the raw input data to variables and store them on cache.
            parse_str($input, static::$cache);
        }

        if ($key === null) {
            return ! empty(static::$cache) ? static::$cache : null;
        }

        return array_key_exists($key, static::$cache) ? static::$cache[$key] : null;
    }

    /**
     * Detect if the HTTP Request is Ajax.
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
     * Detect if the HTTP Request is a GET request.
     *
     * @static static method
     *
     * @return boolean
     */
    public static function isGet()
    {
        return $_SERVER["REQUEST_METHOD"] === "GET";
    }

    /**
     * Detect if the HTTP Request is a HEAD request.
     *
     * @static static method
     *
     * @return boolean
     */
    public static function isHead()
    {
        return $_SERVER["REQUEST_METHOD"] === "HEAD";
    }

    /**
     * Detect if the HTTP Request is a POST request.
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
     * Detect if HTTP Request is PUT request.
     *
     * @static static method
     *
     * @return boolean
     */
    public static function isPut()
    {
        return $_SERVER["REQUEST_METHOD"] === "PUT";
    }

    /**
     * Detect if HTTP Request is DELETE request.
     *
     * @static static method
     *
     * @return boolean
     */
    public static function isDelete()
    {
        return $_SERVER["REQUEST_METHOD"] === "DELETE";
    }

    /**
     * Detect if HTTP Request is OPTIONS request.
     *
     * @static static method
     *
     * @return boolean
     */
    public static function isOptions()
    {
        return $_SERVER["REQUEST_METHOD"] === "OPTIONS";
    }
}
