<?php
/**
 * Encryption - A simple Encrypter Class using OpenSSL.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Encryption;

use Support\Str;
use Encryption\EncryptException;
use Encryption\DecryptException;

use RuntimeException;


class Encrypter
{
    /**
     * The encryption key.
     *
     * @var string
     */
    protected $key;

    /**
     * The algorithm used for encryption.
     *
     * @var string
     */
    protected $cipher;

    /**
     * Create a new Encrypter instance.
     *
     * @param  string $key
     * @param  string $cipher
     * @return void
     */
    public function __construct($key, $cipher = 'AES-256-CBC')
    {
        $key = (string) $key;

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        if (static::supported($key, $cipher)) {
            $this->key = $key;
            $this->cipher = $cipher;
        } else {
            throw new RuntimeException('The only supported ciphers are AES-128-CBC and AES-256-CBC with the correct key lengths.');
        }
    }

    /**
     * Determine if the given key and cipher combination is valid.
     *
     * @param  string $key
     * @param  string $cipher
     * @return bool
     */
    public static function supported($key, $cipher)
    {
        $length = mb_strlen($key, '8bit');

        return ((($cipher === 'AES-128-CBC') && ($length === 16)) || (($cipher === 'AES-256-CBC') && ($length === 32)));
    }

    /**
     * Encrypt the given value.
     *
     * @param  string $value
     * @return string
     *
     * @throws \Encryption\EncryptException
     */
    public function encrypt($value)
    {
        $iv = Str::randomBytes($this->getIvSize());

        $value = \openssl_encrypt(serialize($value), $this->cipher, $this->key, 0, $iv);

        if ($value === false) {
            throw new EncryptException('Could not encrypt the data.');
        }

        $mac = $this->hash($iv = base64_encode($iv), $value);

        return base64_encode(json_encode(compact('iv', 'value', 'mac')));
    }

    /**
     * Decrypt the given value.
     *
     * @param  string $payload
     * @return string
     *
     * @throws \Encryption\DecryptException
     */
    public function decrypt($payload)
    {
        $payload = $this->getJsonPayload($payload);

        $iv = base64_decode($payload['iv']);

        $decrypted = \openssl_decrypt($payload['value'], $this->cipher, $this->key, 0, $iv);

        if ($decrypted === false) {
            throw new DecryptException('Could not decrypt the data.');
        }

        return unserialize($decrypted);
    }

    /**
     * Get the IV size for the cipher.
     *
     * @return int
     */
    protected function getIvSize()
    {
        return 16;
    }

    /**
     * Create a MAC for the given value.
     *
     * @param  string $iv
     * @param  string $value
     * @return string
     */
    protected function hash($iv, $value)
    {
        return hash_hmac('sha256', $iv .$value, $this->key);
    }

    /**
     * Get the JSON array from the given payload.
     *
     * @param  string $payload
     * @return array
     *
     * @throws \Encryption\DecryptException
     */
    protected function getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);

        if (! $payload || $this->invalidPayload($payload)) {
            throw new DecryptException('The payload is invalid.');
        }

        if (! $this->validMac($payload)) {
            throw new DecryptException('The MAC is invalid.');
        }

        return $payload;
    }

    /**
     * Verify that the encryption payload is valid.
     *
     * @param  array|mixed $data
     * @return bool
     */
    protected function invalidPayload($data)
    {
        return ! is_array($data) || ! isset($data['iv']) || ! isset($data['value']) || ! isset($data['mac']);
    }

    /**
     * Determine if the MAC for the given payload is valid.
     *
     * @param  array $payload
     * @return bool
     *
     * @throws \RuntimeException
     */
    protected function validMac(array $payload)
    {
        $bytes = Str::randomBytes(16);

        $calcMac = hash_hmac('sha256', $this->hash($payload['iv'], $payload['value']), $bytes, true);

        return Str::equals(hash_hmac('sha256', $payload['mac'], $bytes, true), $calcMac);
    }

}
