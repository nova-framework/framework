<?php
/**
 * Array Helper Class
 *
 * @author Benjamin von Minden | http://pandory.de
 * @date   August 1, 2015
 * @date updated Sept 19, 2015
 */

namespace Helpers;

/**
 * Collection of array methods.
 */
class Arr
{
    /**
     * Sets an array value.
     *
     * @param array  $array
     * @param string $path
     * @param mixed  $value
     *
     * @return void
     */
    public static function set(array &$array, $path, $value)
    {
        $segments = explode('.', $path);
        while (count($segments) > 1) {
            $segment = array_shift($segments);
            if ( ! isset( $array[$segment] ) || ! is_array($array[$segment])) {
                $array[$segment] = [];
            }
            $array =& $array[$segment];
        }
        $array[array_shift($segments)] = $value;
    }

    /**
     * Search for an array value. Returns TRUE if the array key exists and FALSE if not.
     *
     * @param array  $array
     * @param string $path
     *
     * @return bool
     */
    public static function has(array $array, $path)
    {
        $segments = explode('.', $path);
        foreach ($segments as $segment) {
            if ( ! is_array($array) || ! isset( $array[$segment] )) {
                return false;
            }
            $array = $array[$segment];
        }

        return true;
    }

    /**
     * Returns value from array
     *
     * @param array  $array
     * @param string $path
     * @param mixed  $default
     *
     * @return array|null
     */
    public static function get(array $array, $path, $default = null)
    {
        $segments = explode('.', $path);
        foreach ($segments as $segment) {
            if ( ! is_array($array) || ! isset( $array[$segment] )) {
                return $default;
            }
            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Remove an array value.
     *
     * @param   array  $array Array you want to modify
     * @param   string $path  Array path
     *
     * @return  boolean
     */
    public static function remove(array &$array, $path)
    {
        $segments = explode('.', $path);
        while (count($segments) > 1) {
            $segment = array_shift($segments);
            if ( ! isset( $array[$segment] ) || ! is_array($array[$segment])) {
                return false;
            }
            $array =& $array[$segment];
        }
        unset( $array[array_shift($segments)] );

        return true;
    }

    /**
     * Returns a random value from an array.
     *
     * @param   array $array Array you want to pick a random value from
     *
     * @return  mixed
     */
    public static function rand(array $array)
    {
        return $array[array_rand($array)];
    }

    /**
     * Returns TRUE if the array is associative and FALSE if not.
     *
     * @param   array $array Array to check
     *
     * @return  boolean
     */
    public static function isAssoc(array $array)
    {
        return count(array_filter(array_keys($array), 'is_string')) === count($array);
    }

    /**
     * Returns the values from a single column of the input array, identified by the key.
     *
     * @param   array  $array Array to pluck from
     * @param   string $key   Array key
     *
     * @return  array
     */
    public static function value(array $array, $key)
    {
        return array_map(function ($value) use ($key) {
            return is_object($value) ? $value->$key : $value[$key];
        }, $array);
    }
}
