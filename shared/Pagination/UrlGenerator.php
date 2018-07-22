<?php

namespace Shared\Pagination;

use Nova\Pagination\UrlGenerator as BaseUrlGenerator;


class UrlGenerator extends BaseUrlGenerator
{

    /**
     * Resolve the URL for a given page number.
     *
     * @param  int  $page
     * @param  string  $path
     * @param  array  $query
     * @param  string|null  $fragment
     * @return string
     */
    public function pageUrl($page, $path, array $query, $fragment)
    {
        if ($page > 1) {
            $pageName = $this->getPageName();

            $path = trim($path, '/') .'/' .$pageName .'/' .$page;
        }

        return $this->buildUrl($path, $query, $fragment);
    }
}
