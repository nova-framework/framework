<?php

namespace App\Controllers;

use Nova\Support\Facades\View;
use Nova\Support\Str;

use App\Controllers\BaseController;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class Pages extends BaseController
{
    /**
     * The currently used Theme.
     *
     * @var string
     */
    protected $theme = false; // Disable the support for Themes.

    /**
     * The currently used Layout.
     *
     * @var string
     */
    protected $layout = 'Static';


    public function show($slug = null)
    {
        $segments = array();

        if (! empty($slug)) {
            $segments = explode('/', $slug, 2);
        }

        // Compute the page and subpage.
        list ($page, $subPage) = array_pad($segments, 2, null);

        // Compute the full View name, i.e. 'about-us' -> 'Pages/AboutUs'
        array_unshift($segments, 'pages');

        $view = implode('/', array_map(function ($value)
        {
            return Str::studly($value);

        }, $segments));

        if (View::exists($view)) {
            // We found a proper View for the given URI.
        }

        // We will look for a Home View before to go Exception.
        else if (! View::exists($view = $view .'/Home')) {
            throw new NotFoundHttpException($view);
        }

        $title = Str::title(
            str_replace(array('-', '_'), ' ', $subPage ?: ($page ?: 'Home'))
        );

        return View::make($view)->shares('title', $title);
    }
}
