<?php

namespace Helpers;

/**
 * Cross Site Request Forgery helper
 *
 * @author jimgwhit
 * @date May 23 2015
 */
 
/**
 * Instructions:
 * At the top of the controller where the other "use" statements are place:
 * use Helpers\Csrf;
 * 
 * Just prior to rendering the view for adding or editing data create the token:
 * $data['token'] = Csrf::makeToken();
 * $this->view->renderTemplate('header', $data);
 * $this->view->render('pet/edit', $data, $error); // as an example
 * $this->view->renderTemplate('footer', $data);
 * 
 * In the add or edit view (form) at top of page place:
 * <?php
 * use \Helpers\Session;
 * $token = trim($data['token']);
 * Session::set('token', $token);
 * ?>
 * 
 * Towards the bottom of your add or edit form (same form) before the submit button put:
 * <input type="hidden" name="token" value="<?php echo $token; ?>" />
 * 
 * These lines need to be placed in the controller action for the update or add method:
 * if ($_POST['token'] != Session::get('token')) {
 *     Url::redirect('admin/login'); // or wherever you want to redirect to.
 *    }
 * And that's all 
 */

class Csrf {

    public static function makeToken() {
        $csrf_token = md5(uniqid(rand(), TRUE));
        return $csrf_token;
    }

}
