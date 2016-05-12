<?php

/**
 *  Encrypter for Nova Framework
 *
 * @author Manuel Jhobanny Morillo Ordoñez geomorillo@yahoo.com
 *
 */

namespace Helpers;

class Encrypter
{
    protected static $ivSize = 16;
    protected static $randomBytesLength = 16;
    protected static $algo;
    protected static $hashAlgo = 'sha512';
    protected static $key;
    protected static $strong;

    /**
     * Search supported algorithm from the key length.
     *
     * @param String $key
     * @return boolean|string
     */
    protected static function suportedAlgo($key)
    {
        switch (self::keyLenght($key)) {
            case 16:
                return "AES-128-CBC";

                break;
            case 32:
                return "AES-256-CBC";

                break;
            default:
                return FALSE;

                break;
        }
    }

    /**
     * Setup the key and algo using ENCRYPT_KEY.
     *
     * @return void
     */
    protected static function setConfig()
    {
        self::$key = ENCRYPT_KEY;

        if(empty(self::$key)) {
            throw new \Exception('Please configure the ENCRYPT_KEY.');
        }

        self::$algo = self::suportedAlgo(self::$key);
    }

    /**
     * Retrieve the key length from the string.
     *
     * @param String $key
     * @return Integer
     */
    public static function keyLenght($key)
    {
        return mb_strlen($key, '8bit');
    }

    /**
     * Encrypt the given value.
     *
     * @param  string  $value
     * @return string
     *
     * @throws Exception
     */
    public static function encrypt($value)
    {
        self::setConfig();

        if (self::$algo === FALSE) {
            throw new \Exception('Supported algorithm not found.');
        }

        $iv = mcrypt_create_iv(self::$ivSize, MCRYPT_DEV_URANDOM);

        $value = openssl_encrypt(serialize($value), self::$algo, self::$key, 0, $iv);

        if ($value === false) {
            throw new \Exception('Could not encrypt the data.');
        }

        // Once we have the encrypted the value, we will go ahead and base64_encode the input
        // vector and create the MAC for the encrypted value so we can verify its
        // authenticity. Then, we'll JSON encode the data in a "payload" array.

        $mac = self::hash($iv = base64_encode($iv), $value);

        $json = json_encode(compact('iv', 'value', 'mac'));

        if (! is_string($json)) {
            throw new \Exception('Could not encrypt the data.');
        }

        return base64_encode($json);
    }

    /**
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @return string
     *
     * @throws Exception
     */
    public static function decrypt($payload)
    {
        self::setConfig();

        $payload = self::getJsonPayload($payload);

        $iv = base64_decode($payload['iv']);

        $decrypted = openssl_decrypt($payload['value'], self::$algo, self::$key, 0, $iv);

        if ($decrypted === false) {
            throw new \Exception('Could not decrypt the data.');
        }

        return unserialize($decrypted);
    }

    /**
     * Get the IV size for the cipher.
     *
     * @return int
     */

    /**
     * Get random bytes.
     *
     * @param	int	$length	Output length
     * @return	string
     */
    public static function getRandomBytes($length)
    {
        if (empty($length) OR ! ctype_digit((string) $length)) {
            return FALSE;
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            return openssl_random_pseudo_bytes($length, self::$strong);
        }

        return FALSE;
    }

    /**
     * Return a hash MAC from the IV and value.
     *
     * @param type $iv
     * @param type $value
     * @return string
     */
    public static function hash($iv, $value)
    {

        return hash_hmac(self::$hashAlgo, $iv . $value, self::$key);
    }

    /**
     * Get the JSON array from the given payload.
     *
     * @param  string  $payload
     * @return array
     *
     * @throws Exception
     */
    protected static function getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);

        // If the payload is not valid JSON or does not have the proper keys set, we will
        // assume it is invalid and bail out of the routine since we will not be able
        // to decrypt the given value. We'll also check the MAC for this encryption.
        if (! $payload || ! self::validPayload($payload)) {
            throw new \Exception('The payload is invalid.');
        }

        if (! self::validMac($payload)) {
            throw new \Exception('The MAC is invalid.');
        }

        return $payload;
    }

    /**
     * Verify that the encryption payload is valid.
     *
     * @param  array|mixed  $data
     * @return bool
     */
    protected static function validPayload($data)
    {
        return (is_array($data) && isset($data['iv']) && isset($data['value']) || isset($data['mac']));
    }

    /**
     * Determine if the MAC for the given payload is valid.
     *
     * @param  array  $payload
     * @return bool
     */
    protected static function validMac(array $payload)
    {
        $bytes = self::getRandomBytes(self::$randomBytesLength);

        $calcMac = hash_hmac(self::$hashAlgo, self::hash($payload['iv'], $payload['value']), $bytes, true);
        $knowMac = hash_hmac(self::$hashAlgo, $payload['mac'], $bytes, true);

        return hash_equals($knowMac, $calcMac);
    }

}
