<?php

namespace App\Modules\System\Controllers;

use Nova\Support\Facades\Config;
use Nova\Support\Facades\Cookie;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Session;

use App\Core\Controller;


class Language extends Controller
{

    /**
     * Update the Framework's used Language.
     */
    public function update($language)
    {
        $languages = Config::get('languages');

        // Only set language if it's in the Languages array
        if (in_array($language, array_keys($languages))) {
            Session::set('language', $language);

            // Store the current Language in a Cookie lasting five years.
            Cookie::queue(PREFIX .'language', $language, Cookie::FIVEYEARS);
        }

        return Redirect::back();
    }
}
