<?php
namespace App\Controllers;

use Core\Language as CoreLanguage;
use Helpers\Url;
use Helpers\Cookie;

class Language
{
    public function change($language)
    {
        $codes = CoreLanguage::$codes;

        //only set language if it's in the above array
        if (preg_match ('/[a-z]/', $_COOKIE[PREFIX.'language']) && in_array($language, $codes)) {
            setcookie(PREFIX.'language', ucfirst($language), false, '/', false);
        }

        Url::redirect();
    }
}
