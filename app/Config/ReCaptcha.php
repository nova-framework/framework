<?php
/**
 * ReCaptcha
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Config\Config;


/**
 * Setup the Google reCAPTCHA configuration
 */
Config::set('recaptcha', array(
    'active'  => false,
    'siteKey' => '',
    'secret'  => '',
));
