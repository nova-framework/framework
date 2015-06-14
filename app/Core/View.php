<?php
namespace Core;

/*
 * View - load template pages
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */
class View
{
    /**
     * @var array Array of HTTP headers
     */
    private static $headers = array();

    /**
     * include template file
     * @param  string  $path  path to file from views folder
     * @param  array $data  array of data
     * @param  array $error array of errors
     */
    public static function render($path, $data = false, $error = false)
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }
        require "app/views/$path.php";
    }

    /**
     * include template file
     * @param  string  $path  path to file from Modules folder
     * @param  array $data  array of data
     * @param  array $error array of errors
     */
    public static function renderModule($path, $data = false, $error = false)
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }
        require "app/Modules/$path.php";
    }

    /**
     * return absolute path to selected template directory
     * @param  string  $path  path to file from views folder
     * @param  array   $data  array of data
     * @param  string  $custom path to template folder
     */
    public static function renderTemplate($path, $data = false, $custom = false)
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }

        if ($custom === false) {
            require "app/templates/".TEMPLATE."/$path.php";
        } else {
            require "app/templates/$custom/$path.php";
        }
    }

    /**
     * add HTTP header to headers array
     * @param  string  $header HTTP header text
     */
    public function addHeader($header)
    {
        self::$headers[] = $header;
    }

    /**
    * Add an array with headers to the view.
    * @param array $headers
    */
    public function addHeaders($headers = array())
    {
        foreach ($headers as $header) {
            $this->addHeader($header);
        }
    }
}
