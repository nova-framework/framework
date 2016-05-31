<?php
/**
 * Presenter - Implements a Pagination Presenter using Bootstrap 3.x.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Pagination;

use Pagination\Presenter;


class BootstrapPresenter extends Presenter
{
    /**
     * Get HTML wrapper for a page link.
     *
     * @param  string  $url
     * @param  int  $page
     * @param  string  $rel
     * @return string
     */
    public function getPageLinkWrapper($url, $page, $rel = null)
    {
        $rel = is_null($rel) ? '' : ' class="'.$rel.'"';

        return '<li><a href="'.$url.'"'.$rel.'>'.$page.'</a></li>';
    }

    /**
     * Get HTML wrapper for disabled text.
     *
     * @param  string  $text
     * @return string
     */
    public function getDisabledTextWrapper($text)
    {
        return '<li class="disabled"><span>'.$text.'</span></li>';
    }

    /**
     * Get HTML wrapper for active text.
     *
     * @param  string  $text
     * @return string
     */
    public function getActivePageWrapper($text)
    {
        return '<li class="active"><span>'.$text.'</span></li>';
    }

    /**
     * Get HTML wrapper for the entire paginator.
     *
     * @param  string  $content
     * @return string
     */
    public function getPaginationWrapper($content)
    {
        return '<ul class="pagination">' .$content .'</ul>';
    }

}
