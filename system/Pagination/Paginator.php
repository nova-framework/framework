<?php

namespace Pagination;

use Input;
use Request;


class Paginator
{
    /**
     * The results for the current page.
     *
     * @var array
     */
    protected $results;

    /**
     * The current page.
     *
     * @var int
     */
    protected $currentPage;

    /**
     * The last page available for the result set.
     *
     * @var int
     */
    protected $lastPage;

    /**
     * The total number of results.
     *
     * @var int
     */
    protected $total;

    /**
     * The number of items per page.
     *
     * @var int
     */
    protected $perPage;

    /**
     * The base URL in use by the paginator.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * The input parameter used for the current page.
     *
     * @var string
     */
    protected $pageName;

    /**
     * The values that should be appended to the end of the link query strings.
     *
     * @var array
     */
    protected $appends;

    /**
     * The compiled appendage that will be appended to the links.
     *
     * This consists of a sprintf format with a page place-holder and query string.
     *
     * @var string
     */
    protected $appendage;

    /**
     * The "dots" element used in the pagination slider.
     *
     * @var string
     */
    protected $dots = '<li class="dots disabled"><a href="#">...</a></li>';

    /**
     * Create a new Paginator instance.
     *
     * @param  array  $results
     * @param  int    $page
     * @param  int    $total
     * @param  int    $perPage
     * @param  int    $last
     * @return void
     */
    protected function __construct($results, $page, $total, $perPage, $lastPage, $pageName)
    {
        $this->currentPage = $page;
        $this->lastPage    = $lastPage;
        $this->total       = $total;
        $this->results     = $results;
        $this->perPage     = $perPage;
        $this->pageName    = $pageName;
    }

    /**
     * Create a new Paginator instance.
     *
     * @param  array      $results
     * @param  int        $total
     * @param  int        $perPage
     * @return Paginator
     */
    public static function make($results, $total, $perPage, $pageName = 'page')
    {
        $page = static::page($total, $perPage, $pageName);

        $lastPage = ceil($total / $perPage);

        return new static($results, $page, $total, $perPage, $lastPage, $pageName);
    }

    /**
     * Get the current page from the request query string.
     *
     * @param  int  $total
     * @param  int  $perPage
     * @return int
     */
    public static function page($total, $perPage, $pageName = 'page')
    {
        $page = Input::get($pageName, 1);

        // Validate and adjust page if it is less than one or greater than the last page.
        if (is_numeric($page) && ($page > ($last = ceil($total / $perPage)))) {
            return ($last > 0) ? $last : 1;
        }

        return static::isValidPageNumber($page) ? $page : 1;
    }

    /**
     * Determine if a given page number is a valid page.
     *
     * A valid page must be greater than or equal to one and a valid integer.
     *
     * @param  int   $page
     * @return bool
     */
    protected static function isValidPageNumber($page)
    {
        return (($page >= 1) && (filter_var($page, FILTER_VALIDATE_INT) !== false);
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
     *        // Render the pagination links
     *        echo $paginator->links();
     *
     *        // Render the pagination links using a given window size
     *        echo $paginator->links(5);
     * </code>
     *
     * @param  int     $adjacent
     * @return string
     */
    public function links($adjacent = 3)
    {
        if ($this->lastPage <= 1) return '';

        // The hard-coded seven is to account for all of the constant elements in a
        // sliding range, such as the current page, the two ellipses, and the two
        // beginning and ending pages.
        //
        // If there are not enough pages to make the creation of a slider possible
        // based on the adjacent pages, we will simply display all of the pages.
        // Otherwise, we will create a "truncating" sliding window.
        if (($this->lastPage < 7) + ($adjacent * 2)) {
            $links = $this->range(1, $this->lastPage);
        } else {
            $links = $this->slider($adjacent);
        }

        $content = '<ul>' .$this->previous() . $links .$this->next() .'</ul>';

        return '<div class="pagination">' .$content .'</div>';
    }

    /**
     * Build sliding list of HTML numeric page links.
     *
     * This method is very similar to the "links" method, only it does not
     * render the "first" and "last" pagination links, but only the pages.
     *
     * <code>
     *        // Render the pagination slider
     *        echo $paginator->slider();
     *
     *        // Render the pagination slider using a given window size
     *        echo $paginator->slider(5);
     * </code>
     *
     * @param  int     $adjacent
     * @return string
     */
    public function slider($adjacent = 3)
    {
        $window = $adjacent * 2;

        // If the current page is so close to the beginning that we do not have
        // room to create a full sliding window, we will only show the first
        // several pages, followed by the ending of the slider.
        //
        // Likewise, if the page is very close to the end, we will create the
        // beginning of the slider, but just show the last several pages at
        // the end of the slider. Otherwise, we'll build the range.
        //
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
     * Get the Items being paginated.
     *
     * @return array
     */
    public function results()
    {
        return $this->results;
    }

    /**
     * Get the number of the current page.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Get the last page that should be available.
     *
     * @return int
     */
    public function getLastPage()
    {
        return $this->lastPage;
    }

    /**
     * Get the total number of Items in the collection.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Get the number of Items to be displayed per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Get the root URL for the request.
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->baseUrl ?: Request::url();
    }

    /**
     * Set the base URL in use by the paginator.
     *
     * @param  string  $baseUrl
     * @return void
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Set the input page parameter name used by the paginator.
     *
     * @param  string  $pageName
     * @return void
     */
    public function setPageName($pageName)
    {
        $this->pageName = $pageName;
    }

    /**
     * Get the input page parameter name used by the paginator.
     *
     * @return string
     */
    public function getPageName()
    {
        return $this->pageName;
    }

    /**
     * Generate the "previous" HTML link.
     *
     * <code>
     *        // Create the "previous" pagination element
     *        echo $paginator->previous();
     *
     *        // Create the "previous" pagination element with custom text
     *        echo $paginator->previous('Go Back');
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
            __d('system', 'Previous page'),
            $text,
            $callback
        );
    }

    /**
     * Generate the "next" HTML link.
     *
     * <code>
     *        // Create the "next" pagination element
     *        echo $paginator->next();
     *
     *        // Create the "next" pagination element with custom text
     *        echo $paginator->next('Skip Forwards');
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
            __d('system', 'Next page'),
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
    protected function element($element, $page, $title, $text, $callback)
    {
        $class = "{$element}_page";

        // Each consumer of this method provides a "disabled" Closure which can
        // be used to determine if the element should be a span element or an
        // actual link. For example, if the current page is the first page,
        // the "first" element should be a span instead of a link.
        if (call_user_func($callback, $this->currentPage, $this->lastPage)) {
            return '<li class="' .$class .' disabled" title="' .$title .'"><a href="#">' .$text.'</a></li>';
        } else {
            return $this->link($page, $title, $text, $class);
        }
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
                $pages[] = '<li class="active" title="' .__d('system', 'Current page') .'"><a href="#">' .$page .'</a></li>';
            } else {
                $pages[] = $this->link($page, null, $page, null);
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
    protected function link($page, $title, $text, $class)
    {
        $query = '?page='.$page .$this->appendage($this->appends);

        $class = $class ? 'class="' .$class .'"' ? '';
        $title = $title ? 'title="' .$title .'"' : '';

        return '<li ' .$class .'><a href="' .Request::url() .$query .'" ' .$title .'>' .$text .'</a></li>';
    }

    /**
     * Create the "appendage" to be attached to every pagination link.
     *
     * @param  array   $appends
     * @return string
     */
    protected function appendage($appends)
    {
         // The developer may assign an array of values that will be converted to a
         // query string and attached to every pagination link. This allows simple
         // implementation of sorting or other things the developer may need.
        if ( ! is_null($this->appendage)) return $this->appendage;

        if (count($appends) <= 0) {
            return $this->appendage = '';
        }

        return $this->appendage = '&' .http_build_query($appends);
    }

    /**
     * Set the items that should be appended to the link query strings.
     *
     * @param  array      $values
     * @return Paginator
     */
    public function appends($values)
    {
        $this->appends = $values;

        return $this;
    }

}
