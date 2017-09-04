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

/**
 * A test for the Layout/View logic.
 */
$router->get('test', function ()
{
    // Create a View instance for the Layout content.
    $content = View::make('Test')
        ->shares('title', 'Test')
        ->with('content', 'This is the page content');

    // Create and return the View instance for the Layout.
    return View::make('Layouts/Default')->with('content', $content);
});
