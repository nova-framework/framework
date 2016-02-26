<?php
/**
 * View - load template pages.
 *
 * @author David Carr - dave@daveismyname.com
 *
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */
namespace Core;

/**
 * View class to load template and views files.
 */
class View
{
    /**
     * @var array Array of HTTP headers
     */
    private static $headers = [];

    /**
     * Include template file.
     *
     * @param string $path  path to file from views folder
     * @param array  $data  array of data
     * @param array  $error array of errors
     */
    public static function render($path, $data = false, $error = false)
    {
        self::sendHeaders();

        require SMVC."app/views/$path.php";
    }

    /**
     * Include template file.
     *
     * @param string $path  path to file from Modules folder
     * @param array  $data  array of data
     * @param array  $error array of errors
     */
    public static function renderModule($path, $data = false, $error = false)
    {
        self::sendHeaders();

        require SMVC."app/Modules/$path.php";
    }

    /**
     * Return absolute path to selected template directory.
     *
     * @param string $path   path to file from views folder
     * @param array  $data   array of data
     * @param string $custom path to template folder
     */
    public static function renderTemplate($path, $data = false, $custom = TEMPLATE)
    {
        self::sendHeaders();

        require SMVC."app/templates/$custom/$path.php";
    }

    /**
     * Add HTTP header to headers array.
     *
     * @param string $header HTTP header text
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
    public function addHeaders(array $headers = [])
    {
        self::$headers = array_merge(self::$headers, $headers);
    }

    /**
     * Send headers.
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
