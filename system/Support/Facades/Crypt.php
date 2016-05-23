<?php
/**
 * Database - A Facade to the Database Connection.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Encryption\Encrypter;


class Crypt
{
    /**
     * The Encrypter instance being handled.
     *
     * @var \Encryption\Encrypter|null
     */
    protected static $encrypter;

    /**
     * Return a default Encrypter instance
     *
     * @return \Routing\Redirector
     *
     * @throws Exception
     */
    protected static function getEncrypter()
    {
        if (isset(static::$encrypter)) {
            return static::$encrypter;
        }

        // Prepare a new Encrypter instance.
        $encryptKey = ENCRYPT_KEY;

        if (empty($encryptKey)) {
            throw new \Exception('Please configure the ENCRYPT_KEY.');
        }

        return static::$encrypter = new Encrypter(ENCRYPT_KEY);
    }

    /**
     * Magic Method for calling the methods on the default Encrypter instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = static::getEncrypter();

        // Call the non-static method from the Connection instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
