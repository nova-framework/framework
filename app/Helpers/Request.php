<?php
/**
 * Request Class.
 *
 * @version 2.2
 * @date updated Dec 23, 2015
 */
namespace Helpers;

/**
 * It contains the request information and provide methods to fetch request body.
 */
class Request
{
    /**
     * Safer and better access to $_POST.
     *
     * @param string $key
     * @static static method
     *
     * @return mixed
     */
    public static function post($key)
    {
        if (empty($key)) {
            return isset($_POST) ? $_POST : null;
        } else {
            return array_key_exists($key, $_POST) ? $_POST[$key] : null;
        }
    }

    /**
     * Safer and better access to $_GET.
     *
     * @param string $key
     * @static static method
     *
     * @return mixed
     */
    public static function get($key)
    {
        if (empty($key)) {
            return isset($_GET) ? $_GET : null;
        } else {
            return array_key_exists($key, $_GET) ? $_GET[$key] : null;
        }
    }

    /**
     * Safer and better access to $_FILES.
     *
     * @param string $key
     * @static static method
     *
     * @return mixed
     */
    public static function files($key = null)
    {
        if (empty($key)) {
            return isset($_FILES) ? $_FILES : null;
        } else {
            return array_key_exists($key, $_FILES) ? $_FILES[$key] : null;
        }
    }

    /**
     * Safer and better access to $_GET.
     *
     * @param string $key
     * @static static method
     *
     * @return mixed
     */
    public static function query($key = null)
    {
        if (empty($key)) {
            return isset($_GET) ? $_GET : null;
        } else {
            return array_key_exists($key, $_GET) ? $_GET[$key] : null;
        }
    }

    /**
     * Detect if request is Ajax.
     *
     * @static static method
     *
     * @return bool
     */
    public static function isAjax()
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        }

        return false;
    }

    /**
     * Safer and better access to PUT parameter.
     *
     * @param string $key
     * @static static method
     *
     * @return mixed
     */
    public static function put($key = null)
    {
        parse_str(file_get_contents('php://input'), $_PUT);
        if (empty($key)) {
            return isset($_PUT) ? $_PUT : null;
        } else {
            return array_key_exists($key, $_PUT) ? $_PUT[$key] : null;
        }
    }

    /**
     * Safer and better access to DELETE parameter.
     *
     * @param string $key
     * @static static method
     *
     * @return mixed
     */
    public static function del($key)
    {
        parse_str(file_get_contents('php://input'), $_DEL);

        return array_key_exists($key, $_DEL) ? $_DEL[$key] : null;
    }

    /**
     * Detect if request is POST request.
     *
     * @static static method
     *
     * @return bool
     */
    public static function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Detect if request is GET request.
     *
     * @static static method
     *
     * @return bool
     */
    public static function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Detect if request is PUT request.
     *
     * @static static method
     *
     * @return bool
     */
    public static function isPut()
    {
        return $_SERVER['REQUEST_METHOD'] === 'PUT';
    }

    /**
     * Detect if request is DELETE request.
     *
     * @static static method
     *
     * @return bool
     */
    public static function isDelete()
    {
        return $_SERVER['REQUEST_METHOD'] === 'DELETE';
    }
}
