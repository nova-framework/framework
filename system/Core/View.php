<?php
/**
 * View - load template pages
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Smvc\Core;

/**
 * View class to load template and views files.
 */
class View
{
    private static $legacyPath = false;

    /**
     * @var array Array of HTTP headers
     */
    private static $headers = array();

    /**
     * Enable/disable the legacy View Path style.
     *
     * * @param  bool $enable flag value
     */
    public static function legacyPath($enable)
    {
        self::$legacyPath = $enable;
    }

    /**
     * Include template file.
     *
     * @param  string $path  path to file from views folder
     * @param  array  $data  array of data
     * @param  array  $error array of errors
     */
    public static function render($path, $data = false, $error = false)
    {
        if(self::$legacyPath) {
            $viewPath = APPPATH."Views".DS;
        }
        else if ($path[0] === '/') {
            $viewPath = APPPATH."Views";
        }
        else {
            // Get the Controller instance.
            $instance =& get_instance();

            $viewPath = $instance->viewsPath();
        }

        self::sendHeaders();

        require $viewPath.str_replace('/', DS, $path).".php";
    }

    /**
     * Include template file.
     *
     * @param  string  $path  path to file from Modules folder
     * @param  array $data  array of data
     * @param  array $error array of errors
     */
    public static function renderModule($path, $data = false, $error = false)
    {
        self::sendHeaders();

        require APPPATH."Modules".DS.str_replace('/', DS, $path).".php";
    }

    /**
     * Return absolute path to selected template directory.
     *
     * @param  string  $path  path to file from views folder
     * @param  array   $data  array of data
     * @param  string  $custom path to template folder
     */
    public static function renderTemplate($path, $data = false, $custom = TEMPLATE)
    {
        self::sendHeaders();

        require WEBPATH."templates".DS.$custom.DS.str_replace('/', DS, $path).".php";
    }

    /**
     * Add HTTP header to headers array.
     *
     * @param  string  $header HTTP header text
     */
    public function addHeader($header)
    {
        self::$headers[] = $header;
    }

    /**
     * Add an array with headers to the view.
     *
     * @param array $headers
     */
    public function addHeaders(array $headers = array())
    {
        self::$headers = array_merge(self::$headers, $headers);
    }

    /**
     * Send headers
     */
    public static function sendHeaders()
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }
    }
}
