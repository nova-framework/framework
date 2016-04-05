<?php
/**
 * Useful functions, application wide accessible.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 15th, 2015
 */

use Nova\Language;
use Nova\Core\Controller;
use Nova\Core\View;


/**
 * Get controller instance
 *
 * @return \Nova\Core\Controller
 */
function &get_instance()
{
    return Controller::getInstance();
}

/**
 * View creation helper
 *
 * @param string $view
 * @param array $data
 * @return \Nova\Core\View
 */
function view($view = null, array $data = array())
{
    return View::make($view, $data);
}

/**
 * View Fragment rendering helper
 *
 * @param string $fragment
 * @param array $data
 * @return \Nova\Core\View
 */
function fragment($fragment, array $data = array())
{
    return View::fragment($fragment, $data);
}

/**
 * Render the given View.
 *
 * @param  string  $view
 * @param  array   $data
 * @return string
 */
function render($view, array $data = array())
{
    if (is_null($view)) return '';

    return View::make($view, $data)->fetch();
}

/**
 * Site url helper
 * @param string $path
 * @return string
 */
function site_url($path = '')
{
    return SITE_URL .ltrim($path, '/');
}

// String helpers.

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
