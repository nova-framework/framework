<?php
/**
 * Factory - Implements the Pagination Factory.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Pagination;

use Http\Request;
use Pagination\Paginator;


class Factory
{
    /**
     * The Request instance.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * The number of the current page.
     *
     * @var int
     */
    protected $currentPage;

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
     * Create a new pagination factory.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  string  $pageName
     * @return void
     */
    public function __construct(Request $request, $pageName = 'offset')
    {
        $this->request  = $request;
        $this->pageName = $pageName;
    }

    /**
     * Get a new Paginator instance.
     *
     * @param  array  $items
     * @param  int    $total
     * @param  int|null  $perPage
     * @return \Pagination\Paginator
     */
    public function make(array $items, $total, $perPage = null)
    {
        $paginator = new Paginator($this, $items, $total, $perPage);

        return $paginator->setupPaginationContext();
    }

    /**
     * Get the number of the current page.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        $page = (int) $this->currentPage ?: $this->request->input($this->pageName, 1);

        if (($page < 1) || (filter_var($page, FILTER_VALIDATE_INT) === false)) {
            return 1;
        }

        return $page;
    }

    /**
     * Set the number of the current page.
     *
     * @param  int  $number
     * @return void
     */
    public function setCurrentPage($number)
    {
        $this->currentPage = $number;
    }

    /**
     * Get the root URL for the request.
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->baseUrl ?: $this->request->url();
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
     * Get the active request instance.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set the active request instance.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return void
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

}
