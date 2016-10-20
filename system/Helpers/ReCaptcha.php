<?php
/**
 * ReCaptcha - Manage the Google ReCaptcha Anti-spam protection.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Nova\Helpers;

use Nova\Config\Config;

use Nova\Support\Facades\Request as HttpRequest;


/**
 * ReCaptcha: Google Anti-spam protection for your website.
 */
class ReCaptcha
{
    /**
     * Constant holding the Googe API url.
     */
    const GOOGLEHOST = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Array holding the configuration.
     */
    protected $config;


    public function __construct()
    {
        $this->config = Config::get('recaptcha', array());
    }

    /**
     * Get the Status
     *
     * @return string
     */
    protected function isActive()
    {
        return array_get($this->config, 'active' , false);
    }

    /**
     * Get the Site Key
     *
     * @return string
     */
    protected function getSiteKey()
    {
        return array_get($this->config, 'siteKey' , null);
    }

    /**
     * Get the Secret
     *
     * @return string
     */
    protected function getSecretkey()
    {
        return array_get($this->config, 'secret' , null);
    }

    /**
     * Compare given answer against the generated session.
     *
     * @param  string $response
     * @return boolean
     */
    protected function check($response = null)
    {
        if (! $this->isActive()) return true;

        // Get the Http Request instance.
        $request = HttpRequest::instance();

        // Get the recaptcha response value.
        $response = $response ?: $request->input('g-recaptcha-response', '');

        // Build the query string.
        $query = http_build_query(array(
            'secret'   => $this->getSecretKey(),
            'response' => $response,
            'remoteip' => $request->ip()
        ));

        // Calculate the (complete) request URL.
        $url = static::GOOGLEHOST .'?' .$query;

        // Perform the request to Google server.
        $result = file_get_contents($url);

        // Evaluate the Google server response.
        if ($result !== false) {
            $data = json_decode($result, true);

            if (is_array($data)) {
                return ($data['success'] === true);
            }
        }

        return false;
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
