<?php
/**
 * Useful functions, application wide accessible.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 15th, 2015
 */

use Nova\Language;
use Nova\Core\Controller;


// Return the current Controller instance.

function &get_instance()
{
    return Controller::getInstance();
}

// String helpers.

function str_starts_with($haystack, $needle) {
    return (($needle === '') || (strpos($haystack, $needle) === 0));
}

function str_ends_with($haystack, $needle) {
    return (($needle === '') || (substr($haystack, - strlen($needle)) === $needle));
}

function printStringArray($data)
{
    if($data === null) {
        return;
    }

    foreach($data as $str) {
        echo $str;
    }
}

// A very useful URL helper.

function site_url($path = '')
{
    return DIR .ltrim($path, '/');
}

//
// I18N functions

/**
 * Get formatted translated message back.
 * @param string $message English default message
 * @param mixed $args
 * @return string|void
 */
function __($message, $args = null)
{
    if (! $message) {
        return '';
    }

    $params = (func_num_args() === 2) ? (array)$args : array_slice(func_get_args(), 1);

    $language =& Language::get();

    return $language->translate($message, $params);
}

/**
 * Get formatted translated message back with domain.
 * @param string $domain
 * @param string $message
 * @param mixed $args
 * @return string|void
 */
function __d($domain, $message, $args = null)
{
    if (! $message) {
        return '';
    }

    $params = (func_num_args() === 3) ? (array)$args : array_slice(func_get_args(), 2);

    $language =& Language::get($domain);

    return $language->translate($message, $params);
}
