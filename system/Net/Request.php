<?php
/**
 * Request Class
 *
 * @version 2.2
 * @date updated Sept 19, 2015
 */

namespace Nova\Net;

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

    public static function realIpAddr()
    {
        if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * Safer and better access to $_POST.
     *
     * @param  string   $key
     * @static static method
     *
     * @return mixed
     */
    public static function post($key = null)
    {
        if ($key === null) {
            return isset($_POST) ? $_POST : null;
        }

        return array_key_exists($key, $_POST)? $_POST[$key] : null;
    }

    /**
     * Safer and better access to $_FILES.
     *
     * @param  string   $key
     * @static static method
     *
     * @return mixed
     */
    public static function files($key = null)
    {
        if ($key === null) {
            return isset($_FILES) ? $_FILES : null;
        }

        return array_key_exists($key, $_FILES)? $_FILES[$key] : null;
    }

    /**
     * Safer and better access to $_GET.
     *
     * @param  string   $key
     * @static static method
     *
     * @return mixed
     */
    public static function query($key = null)
    {
        if ($key === null) {
            return isset($_GET) ? $_GET : null;
        }

        return array_key_exists($key, $_GET)? $_GET[$key] : null;
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
        parse_str(file_get_contents("php://input"), $_PUT);

        if ($key == null) {
            return isset($_PUT) ? $_PUT : null;
        }

        return array_key_exists($key, $_PUT) ? $_PUT[$key] : null;
    }

    /**
     * Safer and better access to DELETE parameters.
     *
     * @param  string   $key
     * @static static method
     *
     * @return mixed
     */
    public static function delete($key)
    {
        parse_str(file_get_contents("php://input"), $_DELETE);

        return array_key_exists($key, $_DELETE) ? $_DELETE[$key] : null;
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

    /**
     * Detect if request is PUT request.
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
     * Detect if request is DELETE request.
     *
     * @static static method
     *
     * @return boolean
     */
    public static function isDelete()
    {
        return $_SERVER["REQUEST_METHOD"] === "DELETE";
    }
}
