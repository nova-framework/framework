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
     * @param  array  $errors an array of errors
     * @param  string $class name of the class to apply to the div
     *
     * @return string return the errors inside divs
     */
    public static function display($errors, $class = 'alert alert-danger')
    {
        if (is_array($errors)) {
            foreach ($errors as $error) {
                $row .= "<div class='$class'>$error</div>";
            }

            return $row;
        }

        if (! empty($errors)) {
            return "<div class='$class'>$errors</div>";
        }
    }
}
