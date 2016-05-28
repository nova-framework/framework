<?php
/**
 * Hash - Implements a Password Hasher.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;


class Hash
{
    /**
     * Default crypt cost factor.
     *
     * @var int
     */
    protected static $rounds = 10;


    /**
     * Hash the given value.
     *
     * @param  string  $value
     * @param  array   $options
     * @return string
     *
     * @throws \RuntimeException
     */
    public static function make($password, $algo = PASSWORD_DEFAULT, array $options = array())
    {
        $cost = isset($options['rounds']) ? $options['rounds'] : static::$rounds;

        $hash = password_hash($value, PASSWORD_BCRYPT, array('cost' => $cost));

        if ($hash === false) {
            throw new \RuntimeException("Bcrypt hashing not supported.");
        }

        return $hash;
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param  string  $value
     * @param  string  $hashedValue
     * @param  array   $options
     * @return bool
     */
    public static function check($value, $hashedValue, array $options = array())
    {
        return password_verify($value, $hashedValue);
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string  $hashedValue
     * @param  array   $options
     * @return bool
     */
    public static function needsRehash($hash, $algo = PASSWORD_DEFAULT, array $options = array())
    {
        $cost = isset($options['rounds']) ? $options['rounds'] : static::$rounds;

        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, array('cost' => $cost));
    }

}
