<?php
/**
 * Config - manage the system wide configuration parameters.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 14th, 2015
 */


namespace Nova;

/**
 * Class Config, Will hold the config of the whole framework, including modules.
 *
 * @package Nova
 */
class Config {
    /**
     * @var array
     */
    protected static $settings = array();

    /**
     * Get value
     * @param string $key
     * @return mixed|null
     */
    public static function get($key)
    {
        return isset(self::$settings[$key]) ? self::$settings[$key] : null;
    }

    /**
     * Set value
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        self::$settings[$key] = $value;
    }

    /**
     * Add value to existing key=>value, the value of the key MUST be an array!
     *
     * @param string $key The config main key
     * @param mixed $value Value to append.
     * @param mixed|null $subkey Subkey to append, NULL for pushing onto it with auto-index integer.
     *
     * @return bool Success, should always be true, exceptions will be thrown when having errors in this function.
     * @throws \Exception Throws exceptions when key isn't defined in config, value in the config[key] isn't an array
     *                    or when the optional subkey already is set in the value array.
     */
    public static function add($key, $value, $subkey = null)
    {
        if (! isset(self::$settings[$key])) {
            throw new \OutOfBoundsException("Key value not found in current Config!");
        }
        if (! is_array(self::$settings[$key])) {
            throw new \UnexpectedValueException("Value in the config storage with your provided key isn't an array, we can't add something to it!");
        }

        // Push it to the end of the stack there is already
        if ($subkey === null) {
            array_push(self::$settings[$key], $value);
            return true;
        }

        if (isset(self::$settings[$key][$subkey])) {
            throw new \Exception("Subkey already exists in the current config entry!");
        }

        self::$settings[$key][$subkey] = $value;
        return true;
    }

}
