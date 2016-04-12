<?php
/**
 * Error class.
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */

namespace Core;

/**
 * Error class.
 */
class Error
{
    /**
     * Display errors.
     *
     * @param  array  $error an error of errors
     * @param  string $class name of class to apply to div
     *
     * @return string return the errors inside divs
     */
    public static function display($error, $class = 'alert alert-danger')
    {
        if (is_array($error)) {
            foreach ($error as $error) {
                $row.= "<div class='$class'>$error</div>";
            }
            return $row;
        } else {
            if (isset($error)) {
                return "<div class='$class'>$error</div>";
            }
        }
    }
}
