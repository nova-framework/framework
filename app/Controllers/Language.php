<?php
namespace App\Controllers;

use Core\Config;
use Core\Controller;
use Helpers\Url;

use Cookie;
use Redirect;
use Session;


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
        $languages = Config::get('languages');

        // Only set language if it's in the Languages array
        if (preg_match ('/[a-z]/', $language) && in_array($language, array_keys($languages))) {
            Session::set('language', $language);

            // Store the current Language into Cookie instance.
            $cookie = Cookie::forever(PREFIX .'language', $language);

            // Queue the Cookie instance.
            Cookie::queue($cookie);
        }

        return Redirect::back();
    }
}
