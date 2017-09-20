<?php

use Nova\Http\Request;


/**
 * The static Pages.
 */
$router->get('/', 'Pages@display');

$router->get('pages/{slug}', 'Pages@display')->where('slug', '(.*)');


/**
 * The Language Changer.
 */
$router->get('language/{language}', function (Request $request, $language)
{
    $url = Config::get('app.url');

    $languages = Config::get('languages');

    if (array_key_exists($language, $languages) && Str::startsWith($request->header('referer'), $url)) {
        Session::set('language', $language);

        // Store also the current Language in a Cookie lasting five years.
        Cookie::queue(PREFIX .'language', $language, Cookie::FIVEYEARS);
    }

    return Redirect::back();

})->where('language', '([a-z]{2})');
