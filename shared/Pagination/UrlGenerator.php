<?php

namespace Shared\Pagination;

use Nova\Pagination\UrlGenerator as PaginationUrlGenerator;


class UrlGenerator extends PaginationUrlGenerator
{

    /**
     * Resolve the URL for a given page number.
     *
     * @param  int  $page
     * @return string
     */
    public function url($page)
    {
        $paginator = $this->getPaginator();

        //
        $path = $paginator->getPath();

        if ($page > 1) {
            $pageName = $paginator->getPageName();

            $path = trim($path, '/') .'/' .$pageName .'/' .$page;
        }

        return $this->buildUrl(
            $path, $paginator->getQuery(), $paginator->fragment()
        );
    }
}
