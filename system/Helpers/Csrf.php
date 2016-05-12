<?php

/**
 * Cross Site Request Forgery helper.
 *
 * @author jimgwhit
 * @version 3.0
 */

namespace Helpers;

use Helpers\Session;

/**
 * Instructions:
 * At the top of the controller where the other "use" statements are, place:
 * use Helpers\Csrf;
 *
 * Just prior to rendering the view for adding or editing data, create the CSRF token:
 * $data['csrf_token'] = Csrf::makeToken();
 * $this->view->renderTemplate('header', $data);
 * $this->view->render('pet/edit', $data, $error); // as an example
 * $this->view->renderTemplate('footer', $data);
 *
 * At the bottom of your form, before the submit button put:
 * <input type="hidden" name="csrf_token" value="<?= $data['csrf_token']; ?>" />
 *
 * These lines need to be placed in the controller action to validate CSRF token submitted with the form:
 * if (!Csrf::isTokenValid()) {
 *      Url::redirect('admin/login'); // or wherever you want to redirect to.
 *    }
 * And that's all.
 */
class Csrf {
    /**
     * Retrieve the CSRF token and generate a new one if expired.
     *
     * @access public
     * @static static method
     * @return string
     */
    public static function makeToken($name = 'csrfToken') {
        $max_time = 60 * 60 * 24; // token is valid for 1 day.
        $csrf_token = Session::get($name);
        $stored_time = Session::get($name . '_time');

        if ($max_time + $stored_time <= time() || empty($csrf_token)) {
            $hash = hash('sha512', self::genRandomNumber());
            Session::set($name, $hash);
            Session::set($name . '_time', time());
        }

        return Session::get($name);
    }

    /**
     * Check to see if the CSRF token in session is the same as submitted form.
     *
     * @access public
     * @static static method
     * @return bool
     */
    public static function isTokenValid($name = 'csrfToken') {
        return $_POST[$name] === Session::get($name);
    }
    /**
     * Generate a random number using any available function on the system.
     *
     * @access public
     * @static static method
     * @return integer
     */

    public static function genRandomNumber($size = 32) {
        if (extension_loaded('openssl')) {
            return openssl_random_pseudo_bytes($size);
        }
        if (extension_loaded('mcrypt')) {
            return mcrypt_create_iv($size, MCRYPT_DEV_URANDOM);
        }
        if (function_exists('random_bytes')) {
            return random_bytes($size);
        }
        return mt_rand(0,mt_getrandmax());

    }

}
