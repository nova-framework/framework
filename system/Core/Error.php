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
        $result = '';

        if (is_array($errors)) {
            foreach ($errors as $error) {
                $result .= "<div class='$class'>$error</div>";
            }
        } else if (! empty($errors)) {
            $result = "<div class='$class'>$errors</div>";
        }

        return $result;
    }
}
