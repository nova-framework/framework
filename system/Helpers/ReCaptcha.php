<?php
/**
 * ReCaptcha - Manage the Google ReCaptcha Anti-spam protection.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 21th, 2015
 */

namespace Nova\Helpers;


use Nova\Config;

/**
 * ReCaptcha: Google Anti-spam protection for your website.
 */
class ReCaptcha
{
    /**
     * Constant holding the API url.
     */
    const GOOGLEHOST = 'https://www.google.com/recaptcha/api/siteverify';

    private $recaptcha_sitekey;
    private $recaptcha_secret;

    private $remoteip;


    public function __construct()
    {
        $this->remoteip = $_SERVER['REMOTE_ADDR'];

        $this->recaptcha_sitekey = Config::get('recaptcha_sitekey');
        $this->recaptcha_secret  = Config::get('recaptcha_secret');
    }

    /**
     * Compare given answer against the generated session.
     *
     * @param  string $response
     * @return boolean
     */
    public function checkResponse($response)
    {
        if (empty($response)) {
            return false;
        }

        $google_url = sprintf('%s?secret=%s&response=%s&remoteip=%s',
                            self::GOOGLEHOST,
                            $this->recaptcha_secret,
                            $response,
                            $this->remoteip);

        $response = file_get_contents($google_url);

        if ($response === false) {
            return false;
        }

        $response = json_decode($response, true);

        return ($response['success'] === true);
    }

}
