<?php
/**
 * Cross Site Request Forgery helper
 *
 * @author jimgwhit
 * @date May 23 2015
 * @date updated Sept 19, 2015
 */

namespace Helpers;

use Helpers\Session;

/**
 * Instructions:
 * At the top of the controller where the other "use" statements are place:
 * use Helpers\Csrf;
 *
 * Just prior to rendering the view for adding or editing data create the CSRF token:
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
 * And that's all
 */
class Csrf
{
    /**
     * get CSRF token and generate a new one if expired
     *
     * @access public
     * @static static method
     * @return string
     */
    public static function makeToken()
    {
        $max_time    = 60 * 60 * 24; // token is valid for 1 day
        $csrf_token = Session::get('csrf_token');
        $stored_time  = Session::get('csrf_token_time');

        if ($max_time + $stored_time <= time() || empty($csrf_token)) {
            Session::set('csrf_token', md5(uniqid(rand(), true)));
            Session::set('csrf_token_time', time());
        }

        return Session::get('csrf_token');
    }

    /**
     * checks if CSRF token in session is same as in the form submitted
     *
     * @access public
     * @static static method
     * @return bool
     */
    public static function isTokenValid()
    {
        return $_POST['csrf_token'] === Session::get('csrf_token');
    }
}
