<?php
/**
 * ReCaptcha - Manage the Google ReCaptcha Anti-spam protection.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Helpers;

use Core\Config;

use Request;

/**
 * ReCaptcha: Google Anti-spam protection for your website.
 */
class ReCaptcha
{
    /**
     * Constant holding the API url.
     */
    const GOOGLEHOST = 'https://www.google.com/recaptcha/api/siteverify';

    private $siteKey;
    private $secret;

    private $remoteIp;


    public function __construct()
    {
        $this->remoteIp = Request::server('REMOTE_ADDR');

        //
        $config = Config::get('recaptcha');

        $this->recaptcha_siteKey = $config['siteKey'];
        $this->recaptcha_secret  = $config'secret'];
    }

    /**
     * Get the Site Key
     *
     * @return string
     */
    protected function getSiteKey()
    {
        return $this->siteKey;
    }

    /**
     * Get the Secret
     *
     * @return string
     */
    protected function getSecret()
    {
        return $this->secret;
    }

    /**
     * Compare given answer against the generated session.
     *
     * @param  string $response
     * @return boolean
     */
    protected function checkResponse($response)
    {
        if (empty($response)) {
            return false;
        }

        $google_url = sprintf('%s?secret=%s&response=%s&remoteip=%s',
            self::GOOGLEHOST,
            $this->secret,
            $response,
            $this->remoteIp
        );

        $response = file_get_contents($google_url);

        if ($response === false) {
            return false;
        }

        $response = json_decode($response, true);

        return ($response['success'] === true);
    }

    /**
     * Magic Method for handling dynamic functions.
     *
     * @param  string  $method
     * @param  array   $params
     * @return void|mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = new static();

        return call_user_func_array(array($instance, $method), $params);
    }
}
