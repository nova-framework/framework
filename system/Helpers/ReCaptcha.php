<?php
/**
 * ReCaptcha - Manage the Google ReCaptcha Anti-spam protection.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Helpers;

use Config\Config;

use Helpers\Request;

/**
 * ReCaptcha: Google Anti-spam protection for your website.
 */
class ReCaptcha
{
    /**
     * Constant holding the API url.
     */
    const GOOGLEHOST = 'https://www.google.com/recaptcha/api/siteverify';

    private $active = true;

    private $siteKey;
    private $secret;

    private $remoteIp;


    public function __construct()
    {
        $this->remoteIp = Request::server('REMOTE_ADDR');

        //
        $config = Config::get('recaptcha');

        // Wheter is active or not.
        $this->active  = $config['active'];

        // The Google keys
        $this->siteKey = $config['siteKey'];
        $this->secret  = $config['secret'];
    }

    /**
     * Get the Status
     *
     * @return string
     */
    protected function isActive()
    {
        return $this->active;
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
    protected function check($response = null)
    {
        if(! $this->active) return true;

        //
        $response = $response ?: Request::post('g-recaptcha-response', '');

        if (empty($response)) return false;

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
