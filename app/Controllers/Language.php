<?php
namespace App\Controllers;

use Core\Controller;
use Core\Language as CoreLanguage;
use Helpers\Url;
use Helpers\Cookie;
use Helpers\Session;


class Language extends Controller
{
    /**
     * Call the parent construct.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Change the Framework Language.
     */
    public function change($language)
    {
        // Only set language if it's in the Languages array
        if (preg_match ('/[a-z]/', $language) && in_array($language, CoreLanguage::$codes)) {
            Session::set('language', ucfirst($language));
            // Store the current Language into Cookie.
            Cookie::set(PREFIX .'language', $language);
        }

        Url::redirect();
    }
}
