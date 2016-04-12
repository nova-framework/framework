<?php
/**
 * Functions - small collection of Framework wide interest functions.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 12th, 2016
 */

/**
 * Site url helper
 * @param string $path
 * @return string
 */
function site_url($path = '/')
{
    return SITEURL .ltrim($path, '/');
}

/** String helpers. */

/**
 * Test for string starts with
 * @param $haystack
 * @param $needle
 * @return bool
 */
function str_starts_with($haystack, $needle)
{
    return (($needle === '') || (strpos($haystack, $needle) === 0));
}

/**
 * Test for string ends with
 * @param $haystack
 * @param $needle
 * @return bool
 */
function str_ends_with($haystack, $needle)
{
    return (($needle === '') || (substr($haystack, - strlen($needle)) === $needle));
}

/** Common data lookup methods. */

/**
 * print_r call wrapped in pre tags
 *
 * @param  string or array $data
 */
function pr($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

/**
 * var_dump call
 *
 * @param  string or array $data
 */
function vd($data)
{
    var_dump($data);
}

/**
 * strlen call - count the lengh of the string.
 *
 * @param  string $data
 * @return string return the count
 */
function sl($data)
{
    return strlen($data);
}

/**
 * strtoupper - convert string to uppercase.
 *
 * @param  string $data
 * @return string
 */
function stu($data)
{
    return strtoupper($data);
}

/**
 * strtolower - convert string to lowercase.
 *
 * @param  string $data
 * @return string
 */
function stl($data)
{
    return strtolower($data);
}

/**
 * ucwords - the first letter of each word to be a capital.
 *
 * @param  string $data
 * @return string
 */
function ucw($data)
{
    return ucwords($data);
}

/**
 * key - this will generate a 32 character key
 * @return string
 */
function createKey($length = 32)
{
    $chars = "!@#$%^&*()_+-=ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
    $key = "";

    for ($i = 0; $i < $length; $i++) {
        $key .= $chars{rand(0, strlen($chars) - 1)};
    }

    return $key;
}
