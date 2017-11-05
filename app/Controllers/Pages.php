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
        list ($view, $title) = $this->parseSlug($slug, 'pages');

        return View::make($view)
            ->shares('title', ($title != 'Pages') ? $title : 'Home');
    }

    public function showTutorial($slug = null)
    {
        list ($view, $title) = $this->parseSlug($slug, 'tutorials');

        return View::make('Static')
            ->shares('title', $title)
            ->nest('sidebar', 'Tutorials/Contents')
            ->nest('content', $view);
    }

    protected function parseSlug($slug, $type)
    {
        $segments = explode('/', $slug, 2);

        // Compute the page and subpage.
        list ($page, $subpage) = array_pad($segments, 2, null);

        // Compute the full View name, i.e. 'about-us' -> 'Pages/AboutUs'
        array_unshift($segments, $type);

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

        $title = Str::title(
            str_replace(array('-', '_'), ' ', $subpage ?: ($page ?: $type))
        );

        return array($view, $title);
    }
}
