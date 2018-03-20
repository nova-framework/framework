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
        $segments = explode('/', $slug, 2);

        // Compute the page and subpage.
        list ($page, $subpage) = array_pad($segments, 2, null);

        // Compute the full View name, i.e. 'about-us' -> 'Pages/AboutUs'
        array_unshift($segments, 'pages');

        $view = implode('/', array_map(function ($value)
        {
            return Str::studly($value);

        }, $segments));

        if (! View::exists($view)) {
            // We will look for the Home view before to go Exception.

            if (! View::exists($view = $view .'/Home')) {
                throw new NotFoundHttpException($view);
            }
        }

        if (! is_null($subpage)) {
            $title = $subpage;
        } else {
            $title = $page ?: 'Home';
        }

        $title = Str::title(
            str_replace(array('-', '_'), ' ', $title)
        );

        return View::make($view)->shares('title', $title);
    }
}
