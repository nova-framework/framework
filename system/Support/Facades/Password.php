<?php
/**
 * Password - A Facade to the Auth System's Password Broker.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;


class Password
{
    /**
     * Magic Method for calling the methods on the default Password Broker instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
    }
}
