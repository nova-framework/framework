<?php
/**
 * Presenter - Implements a Pagination Presenter using Bootstrap 3.x.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Pagination;

use Pagination\Paginator;


class Presenter
{
    /**
     * The Paginator instance.
     *
     * @var \Pagination\Paginator
     */
    protected $paginator;

    /**
     * Get the current page for the request.
     *
     * @var int
     */
    protected $currentPage;

    /**
     * Get the last available page number.
     *
     * @return int
     */
    protected $lastPage;

    /**
     * The "dots" element used in the pagination slider.
     *
     * @var string
     */
    protected $dots = '<li class="dots disabled"><a href="#">...</a></li>';

    
    /**
     * Create a new Presenter instance.
     *
     * @param  \Pagination\Paginator  $paginator
     * @return void
     */
    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;

        $this->currentPage = $paginator->getCurrentPage();

        $this->lastPage = $paginator->getLastPage();
    }

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
     * @param  string  $items
     * @return string
     */
    public function getPaginationWrapper($content)
    {
        return '<nav><ul class="pagination">' .$content .'</ul></nav>';
    }

    /**
     * Create the HTML pagination links.
     *
     * Typically, an intelligent, "sliding" window of links will be rendered based
     * on the total number of pages, the current page, and the number of adjacent
     * pages that should rendered. This creates a beautiful paginator similar to
     * that of Google's.
     *
     * Example: 1 2 ... 23 24 25 [26] 27 28 29 ... 51 52
     *
     * If you wish to render only certain elements of the pagination control,
     * explore some of the other public methods available on the instance.
     *
     * <code>
     *      // Render the Pagination links
     *      echo $paginator->links();
     *
     *      // Render the Pagination links using a given window size
     *      echo $paginator->links(5);
     * </code>
     *
     * @param  int     $adjacent
     * @return string
     */
    public function links($adjacent = 3)
    {
        if ($this->lastPage <= 1) return '';

        if (($this->lastPage < 7) + ($adjacent * 2)) {
            $links = $this->range(1, $this->lastPage);
        } else {
            $links = $this->slider($adjacent);
        }

        $content = $this->previous() . $links .$this->next();

        return $this->getPaginationWrapper($content);
    }

    /**
     * Build sliding list of HTML numeric page links.
     *
     * This method is very similar to the "links" method, only it does not
     * render the "first" and "last" pagination links, but only the pages.
     *
     * <code>
     *      // Render the pagination slider
     *      echo $paginator->slider();
     *
     *      // Render the pagination slider using a given window size
     *      echo $paginator->slider(5);
     * </code>
     *
     * @param  int     $adjacent
     * @return string
     */
    public function slider($adjacent = 3)
    {
        $window = $adjacent * 2;

        // Example: 1 [2] 3 4 5 6 ... 23 24
        if ($this->currentPage <= $window) {
            return $this->range(1, $window + 2).' '.$this->ending();
        }
        // Example: 1 2 ... 32 33 34 35 [36] 37
        else if ($this->currentPage >= $this->lastPage - $window) {
            return $this->beginning() .' ' .$this->range($this->lastPage - $window - 2, $this->lastPage);
        }

        // Example: 1 2 ... 23 24 25 [26] 27 28 29 ... 51 52
        $content = $this->range($this->currentPage - $adjacent, $this->currentPage + $adjacent);

        return $this->beginning() .' ' .$content .' ' .$this->ending();
    }

    /**
     * Generate the "previous" HTML link.
     *
     * <code>
     *      // Create the "previous" pagination element
     *      echo $paginator->previous();
     *
     *      // Create the "previous" pagination element with custom text
     *      echo $paginator->previous('Go Back');
     * </code>
     *
     * @param  string  $text
     * @return string
     */
    public function previous($text = null)
    {
        $text = $text ?: '&laquo;';

        $callback = function($page, $lastPage) { return ($page <= 1); };

        return $this->element(
            __FUNCTION__,
            $this->currentPage - 1,
            $text,
            $callback
        );
    }

    /**
     * Generate the "next" HTML link.
     *
     * <code>
     *      // Create the "next" pagination element
     *      echo $paginator->next();
     *
     *      // Create the "next" pagination element with custom text
     *      echo $paginator->next('Skip Forwards');
     * </code>
     *
     * @param  string  $text
     * @return string
     */
    public function next($text = null)
    {
        $text = $text ?: '&raquo;';

        $callback = function($page, $lastPage) { return ($page >= $lastPage); };

        return $this->element(
            __FUNCTION__,
            $this->currentPage + 1,
            $text,
            $callback
        );
    }

    /**
     * Create a chronological pagination element, such as a "previous" or "next" link.
     *
     * @param  string   $element
     * @param  int      $page
     * @param  string   $text
     * @param  Closure  $callback
     * @return string
     */
    protected function element($element, $page, $text, $disabled)
    {
        if (call_user_func($disabled, $this->currentPage, $this->lastPage)) {
            return $this->getDisabledTextWrapper($text);
        }

        $rel = "{$element}_page";

        return $this->link($page, $text, $rel);
    }

    /**
     * Build the first two page links for a sliding page range.
     *
     * @return string
     */
    protected function beginning()
    {
        return $this->range(1, 2) .' ' .$this->dots;
    }

    /**
     * Build the last two page links for a sliding page range.
     *
     * @return string
     */
    protected function ending()
    {
        return $this->dots .' ' .$this->range($this->lastPage - 1, $this->lastPage);
    }

    /**
     * Build a range of numeric pagination links.
     *
     * For the current page, an HTML span element will be generated instead of a link.
     *
     * @param  int     $start
     * @param  int     $end
     * @return string
     */
    protected function range($start, $end)
    {
        $pages = array();

        // To generate the range of page links, we will iterate through each page
        // and, if the current page matches the page, we will generate a span,
        // otherwise we will generate a link for the page. The span elements
        // will be assigned the "current" CSS class for convenient styling.
        for ($page = $start; $page <= $end; $page++) {
            if ($this->currentPage == $page) {
                $pages[] = $this->getActivePageWrapper($page);
            } else {
                $pages[] = $this->link($page, $page, null);
            }
        }

        return implode(' ', $pages);
    }

    /**
     * Create a HTML page link.
     *
     * @param  int     $page
     * @param  string  $text
     * @param  string  $class
     * @return string
     */
    protected function link($page, $text, $rel)
    {
        $url = $this->paginator->getUrl($page);

        return $this->getPageLinkWrapper($url, $text, $rel);
    }
}
