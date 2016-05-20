<?php
/**
 * Functions - small collection of Framework wide interest functions.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 12th, 2016
 */

use Helpers\Url;
use Helpers\Encrypter;
use Support\Facades\Language;

/**
 * Site URL helper
 * @param string $path
 * @return string
 */
function site_url($path = '/')
{
    return SITEURL .ltrim($path, '/');
}

/**
 * Resource URL helper
 * @param string $path
 * @param string|null $module
 * @return string
 */
function resource_url($path, $module = null)
{
    return Url::resourcePath($module) .ltrim($path, '/');
}

/**
 * Template URL helper
 * @param string $path
 * @param string $template
 * @param string $folder
 * @return string
 */
function template_url($path, $template = TEMPLATE, $folder = '/assets/')
{
    return Url::templatePath($template, $folder) .ltrim($path, '/');
}

//
// I18N functions

/**
 * Get the formatted and translated message back.
 *
 * @param string $message English default message
 * @param mixed $args
 * @return string|void
 */
function __($message, $args = null)
{
    if (! $message) return '';

    //
    $params = (func_num_args() === 2) ? (array)$args : array_slice(func_get_args(), 1);

    return Language::getInstance()
        ->translate($message, $params);
}

/**
 * Get the formatted and translated message back with Domain.
 *
 * @param string $domain
 * @param string $message
 * @param mixed $args
 * @return string|void
 */
function __d($domain, $message, $args = null)
{
    if (! $message) return '';

    //
    $params = (func_num_args() === 3) ? (array)$args : array_slice(func_get_args(), 2);

    return Language::getInstance($domain)
        ->translate($message, $params);
}

/** Array helpers. */

/**
 * Get an item from an array using "dot" notation.
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $default
 * @return mixed
 */
function array_get($array, $key, $default = null)
{
    if (is_null($key)) return $array;

    if (isset($array[$key])) return $array[$key];

    foreach (explode('.', $key) as $segment) {
        if (! is_array($array) || ! array_key_exists($segment, $array)) {
            return $default;
        }

        $array = $array[$segment];
    }

    return $array;
}

/**
 * Set an array item to a given value using "dot" notation.
 *
 * If no key is given to the method, the entire array will be replaced.
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $value
 * @return array
 */
function array_set(&$array, $key, $value)
{
    if (is_null($key)) return $array = $value;

    $keys = explode('.', $key);

    while (count($keys) > 1) {
        $key = array_shift($keys);

        if ( ! isset($array[$key]) || ! is_array($array[$key])) {
            $array[$key] = array();
        }

        $array =& $array[$key];
    }

    $array[array_shift($keys)] = $value;

    return $array;
}

/**
 * Remove an array item from a given array using "dot" notation.
 *
 * @param  array   $array
 * @param  string  $key
 * @return void
 */
function array_forget(&$array, $key)
{
    $keys = explode('.', $key);

    while (count($keys) > 1) {
        $key = array_shift($keys);

        if ( ! isset($array[$key]) || ! is_array($array[$key])) {
            return;
        }

        $array =& $array[$key];
    }

    unset($array[array_shift($keys)]);
}

/**
 * Get the first element of an array.
 *
 * @param  array  $array
 * @return mixed
 */
function head($array)
{
    return reset($array);
}

/** String helpers. */

/**
 * Determine if a given string matches a given pattern.
 *
 * @param  string  $pattern
 * @param  string  $value
 * @return bool
 */
function str_is($pattern, $value)
{
    if ($pattern != $value) {
        $pattern = str_replace('\*', '.*', preg_quote($pattern, '#')) .'\z';

        return (bool) preg_match('#^' .$pattern .'#', $value);
    }

    return true;
}

/**
 * Determine if a given string contains a given substring.
 *
 * @param  string  $haystack
 * @param  string|array  $needles
 * @return bool
 */
function str_contains($haystack, $needles)
{
    foreach ((array) $needles as $needle) {
        if (($needle != '') && (strpos($haystack, $needle) !== false)) {
            return true;
        }
    }

    return false;
}

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

/**
 * Generate a random alpha-numeric string.
 *
 * @param  int     $length
 * @return string
 *
 * @throws \RuntimeException
 */
function str_random($length = 16)
{
    // Generate a more truly "random" alpha-numeric string.
    $bytes = Encrypter::randomBytes($length * 2);

    return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
}

/**
 * Class name helper
 * @param string $className
 * @return string
 */
function class_basename($className)
{
    return basename(str_replace('\\', '/', $className));
}

/**
 * Determine if the given object has a toString method.
 *
 * @param  object  $object
 * @return bool
 */
function str_object($object)
{
    return (is_object($object) && method_exists($object, '__toString'));
}

/**
 * Return the given object.
 *
 * @param  mixed  $object
 * @return mixed
 */
function with($object)
{
        return $object;
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
 * strlen call - count the length of the string.
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
