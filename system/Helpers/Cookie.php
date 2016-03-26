<?php
/**
 * Simple cookie class
 *
 * @author Jhobanny Morillo geomorillo@yahoo.com
 * @version 3.0
 */

namespace Helpers;

/**
 * Class Cookie
 */
class Cookie
{
    const FOURYEARS = 126144000;

    /**
     * Doest the cookie with key exists?
     *
     * @param string $key
     * @return bool
     */
    public static function exists($key)
    {
        if (isset($_COOKIE[$key])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Set cookie value and optionals: expiry, path and domain.
     *
     * @param string $key
     * @param mixed $value
     * @param int $expiry
     * @param string $path
     * @param bool $domain
     * @return bool
     */
    public static function set($key, $value, $expiry = self::FOURYEARS, $path = "/", $domain = false)
    {
        $retval = false;

        if (! headers_sent()) {
            if ($domain === false) {
                $domain = $_SERVER['HTTP_HOST'];
            }

            if ($expiry === -1) {
                $expiry = 1893456000; // Lifetime = 2030-01-01 00:00:00
            } else if (is_numeric($expiry)) {
                $expiry += time();
            } else {
                $expiry = strtotime($expiry);
            }

            $retval = @setcookie($key, $value, $expiry, $path, $domain);

            if ($retval) {
                $_COOKIE[$key] = $value;
            }
        }

        return $retval;
    }

    /**
     * Get cookie value
     *
     * @param $key
     * @param string $default
     * @return string|mixed
     */
    public static function get($key, $default = '')
    {
        return (isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default);
    }

    /**
     * Get the cookie array
     * @return array
     */
    public static function display()
    {
        return $_COOKIE;
    }

    /**
     * Destroy cookie entry
     * @param string $key
     * @param string $path Optional
     * @param string $domain Optional
     */
    public static function destroy($key, $path = "/", $domain = "")
    {
        unset($_COOKIE[$key]);

        setcookie($key, '', time() - 3600, $path, $domain);
    }
}
