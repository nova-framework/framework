<?php
/**
 * Config - manage the system wide configuration parameters.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 12th, 2016
 */

namespace Core;


class Config
{
    /**
     * @var array
     */
    protected static $settings = array();

    /**
     * Get the value.
     * @param string $key
     * @return mixed|null
     */
    public static function get($key)
    {
        return isset(self::$settings[$key]) ? self::$settings[$key] : null;
    }

    /**
     * Set the value.
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        self::$settings[$key] = $value;
    }
}
