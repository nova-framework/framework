<?php
/**
 * Useful functions, application wide accessible.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 15th, 2015
 */

use Smvc\Language;


// A very useful URL helper.

function site_url($path = '')
{
    return DIR .ltrim($path, '/');
}

//
// I18N functions

function __($message, $args = null)
{
    if (! $message) {
        return;
    }

    $params = (func_num_args() === 2) ? (array)$args : array_slice(func_get_args(), 1);

    $language =& Language::get();

    return $language->translate($message, $params);
}

function __d($domain, $message, $args = null)
{
    if (! $message) {
        return;
    }

    $params = (func_num_args() === 3) ? (array)$args : array_slice(func_get_args(), 2);

    $language =& Language::get($domain);

    return $language->translate($message, $params);
}
