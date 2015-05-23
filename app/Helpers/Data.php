<?php
namespace Helpers;

/*
 * data Helper - common data lookup methods
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 1.0
 * @date March 28, 2015
 * @date May 18 2015
 */
class Data
{
    /**
     * print_r call wrapped in pre tags
     * @param  string or array $data
     */
    public static function pr($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }

    /**
     * var_dump call
     * @param  string or array $data
     */
    public static function vd($data)
    {
        var_dump($data);
    }

    /**
     * strlen call - count the lengh of the string
     * @param  string $data
     * @return string return the count
     */
    public static function sl($data)
    {
        return strlen($data);
    }

    /**
     * strtoupper - convert string to uppercase
     * @param  string $data
     * @return string
     */
    public static function sup($data)
    {
        return strtoupper($data);
    }

    /**
     * strtolower - convert string to lowercase
     * @param  string $data
     * @return string
     */
    public static function slw($data)
    {
        return strtolower($data);
    }

    /**
     * ucwords - the first letter of each word to be a capital
     * @param  string $data
     * @return string
     */
    public static function ucw($data)
    {
        return ucwords($data);
    }
}
