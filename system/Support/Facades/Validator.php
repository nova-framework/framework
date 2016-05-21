<?php
/**
 * Validator - A Facade to the Validator's Factory.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Database\Connection;
use Validation\Translator;
use Validation\DatabasePresenceVerifier;
use Validation\Factory;


class Validator
{
    /**
     * The Factory instance being handled.
     *
     * @var \Validation\Factory|null
     */
    protected static $factory;

    /**
     * Wheter or not is verified the data presence in Database.
     *
     * @var bool
     */
    protected static $verifyPresence = true;


    /**
     * Wheter or not is verified the data presence in Database.
     *
     * @param bool $what
     */
    public static function verifyPresence($what)
    {
        static::$verifyPresence = $what;
    }

    /**
     * Return a Validator Factory instance
     *
     * @return \Validation\Factory
     */
    public static function getFactory()
    {
        if (! isset(static::$factory)) {
            // Setup the local Translator.
            $translator = new Translator();

            // Setup the Factory instance.
            static::$factory = new Factory($translator);

            // Setup the Presence Verifier on the Factory instance.
            if (static::$verifyPresence) {
                $connection = Connection::getInstance();

                $presenceVerifier = new DatabasePresenceVerifier($connection);

                static::$factory->setPresenceVerifier($presenceVerifier);
            }
        }

        // Return the now configured Factory instance.
        return static::$factory;
    }

    /**
     * Magic Method for calling the methods on the Factory instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = static::getFactory();

        // Call the non-static method from the Dispatcher instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
