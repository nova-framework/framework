<?php
namespace App\Controllers;

use Core\Language as CoreLanguage;
use Helpers\Url;
use Helpers\Cookie;

class Language
{
    public function change($language)
    {
        // Only set language if it's in the Languages array
        if (preg_match ('/[a-z]/', $language) && in_array($language, CoreLanguage::$codes)) {
            Cookie::set(PREFIX .'language', $language);
        }

        Url::redirect();
    }
}
