<?php
/**
 * Functions - small collection of Framework wide interest functions.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 12th, 2016
 */

use Helpers\Url;


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
 * @param  object  $value
 * @return bool
 */
function str_object($value)
{
    return (is_object($value) && method_exists($value, '__toString'));
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
    if (function_exists('openssl_random_pseudo_bytes')) {
        // Generate a more truly "random" alpha-numeric string.
        $bytes = openssl_random_pseudo_bytes($length * 2);

        if ($bytes === false) {
            throw new \RuntimeException('Unable to generate random string.');
        }

        return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
    }

    //Generate a "random" alpha-numeric string.
    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
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
