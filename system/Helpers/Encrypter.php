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

        $iv = self::randomBytes(self::$ivSize);
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
   public static function randomBytes($length = 32) {
        if (empty($length) OR ! ctype_digit((string) $length)) {
            return FALSE;
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            return openssl_random_pseudo_bytes($length, self::$strong);
        }
        return self::genRandomBytes($length);// fallback to "low security"

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
    
      /**
     * Generates a random png image with noise so we can extract binary data as a pool of bytes
     * @return void
     */
    protected static function imageNoise() {
        $data = "\0\0"; // wbmp starts with \0\0 
        //random with and size
        $width = mt_rand(50, 100);
        $height = mt_rand(50, 100);

        $data .= chr($width); // next the width 
        $data .= chr($height); // and height 
// and then image data 
        $wi = floor($width / 8);
        $wj = $width - $wi * 8;
        for ($i = 0; $i < $height; $i++) {
            for ($j = 0; $j < $wi; $j++)
                $data .= chr(mt_rand(0, 255));
            if ($wj)
                $data .= chr(mt_rand(0, 1 << $wj - 1) << (8 - $wj));
        }

        imagepng(imagecreatefromstring($data), 'assets/image.png');
    }
     /**
     * If No build-in crypto randomness function found like /dev/urandom. We collect any entropy 
     * available in the PHP core PRNGs along with some filesystem info and memory
     * stats.We gather more entropy by measuring the time needed to compute
     * a number of SHA-1 hashes.  We get the data from an random generated image
     * This function will not be used for encryption but as a fallback for other
     * functions inside the framework.
     * @param integer $lenght
     * @return Bytes
     */
    protected static function genRandomBytes($lenght = 32) {
        self::imageNoise();
        $str = '';
        $bits_per_round = 2; // bits of entropy collected in each clock drift round
        $msec_per_round = 400; // expected running time of each round in microseconds
        $hash_len = 20; // SHA-1 Hash length
        $total = $lenght; // total bytes of entropy to collect
        $handle = @fopen('assets/image.png', 'rb');
        if ($handle && function_exists('stream_set_read_buffer')) {
            @stream_set_read_buffer($handle, 0);
        }

        do {
            $bytes = ($total > $hash_len) ? $hash_len : $total;
            $total -= $bytes;
            //collect any entropy available from the PHP system and filesystem
            $entropy = rand() . uniqid(mt_rand(), true) . $SSLstr;
            $entropy .= implode('', @fstat(@fopen(__FILE__, 'r')));
            $entropy .= memory_get_usage() . getmypid();
            $entropy .= serialize($_ENV) . serialize($_SERVER);
            if ($handle) {
                $entropy .= @fread($handle, $bytes);
            }
            for ($i = 0; $i < 3; $i++) {
                $c1 = microtime(true);
                $var = sha1(mt_rand());
                for ($j = 0; $j < 50; $j++) {
                    $var = sha1($var);
                }
                $c2 = microtime(true);
                $entropy .= $c1 . $c2;
            }
            $rounds = (int) ($msec_per_round * 50 / (int) (($c2 - $c1) * 1000000));

            // Take the additional measurements. On average we can expect
            // at least $bits_per_round bits of entropy from each measurement.
            $iter = $bytes * (int) (ceil(8 / $bits_per_round));
            for ($i = 0; $i < $iter; $i++) {
                $c1 = microtime();
                $var = sha1(mt_rand());
                for ($j = 0; $j < $rounds; $j++) {
                    $var = sha1($var);
                }
                $c2 = microtime();
                $entropy .= $c1 . $c2;
            }
            // We assume sha1 is a deterministic extractor for the $entropy variable.
            $str .= sha1($entropy, true);
        } while ($lenght > strlen($str));

        if ($handle) {
            @fclose($handle);
        }
        return substr($str, 0, $lenght);
    }


}
