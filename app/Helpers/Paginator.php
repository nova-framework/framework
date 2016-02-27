<?php
/**
 * PHP Pagination Class
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date updated Sept 19, 2015
 */

namespace Helpers;

/**
 * Split records into multiple pages.
 */
class Paginator
{

    /**
     * Set the number of items per page.
     *
     * @var numeric
     */
    private $perPage;

    /**
     * Set get parameter for fetching the page number.
     *
     * @var string
     */
    private $instance;

    /**
     * Sets the page number.
     *
     * @var numeric
     */
    private $page;

    /**
     * Set the limit for the data source.
     *
     * @var string
     */
    private $limit;

    /**
     * Set the total number of records/items.
     *
     * @var numeric
     */
    private $totalRows = 0;

    /**
     *  __construct
     *
     *  Pass values when class is istantiated.
     *
     * @param numeric  $perPage  sets the number of iteems per page
     * @param numeric  $instance sets the instance for the GET parameter
     */
    public function __construct($perPage, $instance)
    {
        $this->instance = $instance;
        $this->perPage = $perPage;
        $this->setInstance();
    }

    /**
     * getStart
     *
     * Creates the starting point for limiting the dataset.
     *
     * @return numeric
     */
    public function getStart()
    {
        return ($this->page * $this->perPage) - $this->perPage;
    }

    /**
     * getInstance
     *
     * Gets the current page number if needed anywhere in your application.
     *
     * @var numeric
     */
    public function getInstance()
    {
        return $this->page;
    }

    /**
     * setInstance
     *
     * Sets the instance parameter, if numeric value is 0 then set to 1.
     *
     * @var numeric
     */
    private function setInstance()
    {
        $this->page = (int) (!isset($_GET[$this->instance]) ? 1 : $_GET[$this->instance]);
        $this->page = ($this->page == 0 ? 1 : $this->page);
    }

    /**
     * setTotal
     *
     * Collect a numberic value and assigns it to the totalRows.
     *
     * @param int $totalRows holds the total number of rows
     */
    public function setTotal($totalRows)
    {
        $this->totalRows = $totalRows;
    }

    /**
     * getLimit
     *
     * Returns the limit for the data source, calling the getStart method and passing in the number of items perp page.
     *
     * @return string
     */
    public function getLimit()
    {
        return "LIMIT " . $this->getStart() . ", $this->perPage";
    }

    /**
     * getLimit2 and getPerPage are used together
     * when using the Eloquent Query Builder
     * for the skip and take parameters.
     *
     * There are also other ORM's that need the skip and take
     * parameters separated.
     *
     * Example in controller method calling model method:
     *
     * $data['pets'] = $this->pet->getPets($pages->getLimit2(), $pages->getPerPage(), $petSearch);
     *
     * Example model method using Eloquent Query Builder:
     *
     * public function getPets($offset = "", $rowsPerPage = "", $petSearch = "")
     * {
     *     $petsearch = $petsearch . "%";
     *
     *     return Capsule::table('pets')
     *                     ->where('petName', 'like', $petsearch)
     *                     ->orderBy('petName', 'asc')
     *                     ->skip($offset)->take($rowsPerPage)->get();
     * }
     *
     * Also see the file in the helpers folder page_eloq.md for more help.
     *
     * @var numeric
     */
    public function getLimit2()
    {
        return $this->getStart();
    }

    /**
     * Get Per Page.
     *
     * @return int returns the number of records per page.
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * pageLinks
     *
     * Create the html links for navigating through the dataset.
     *
     * @param string $path optionally set the path for the link
     * @param string $ext optionally pass in extra parameters to the GET
     *
     * @return string returns the html menu
     */
    public function pageLinks($path = '?', $ext = null)
    {
        $adjacents = "2";
        $prev = $this->page - 1;
        $next = $this->page + 1;
        $lastpage = ceil($this->totalRows / $this->perPage);
        $lpm1 = $lastpage - 1;

        $pagination = "";
        if ($lastpage > 1) {
            $pagination .= "<nav>";
            $pagination .= "<ul class='pagination'>";
            if ($this->page > 1) {
                $pagination.= "<li><a href='" . $path . "$this->instance=$prev" . "$ext' aria-label='Previous'><span aria-hidden='true'>&laquo;</span></a></li>";
            } else {
                $pagination.= "<li class='disabled'><a href='#' aria-label='Previous'><span aria-hidden='true'>&laquo;</span></a></li>";
            }

            if ($lastpage < 7 + ($adjacents * 2)) {
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $this->page) {
                        $pagination.= "<li class='active'><span>$counter <span class='sr-only'>(current)</span></span></li>";
                    } else {
                        $pagination.= "<li><a href='" . $path . "$this->instance=$counter" . "$ext'>$counter</a></li>";
                    }
                }
            } elseif ($lastpage > 5 + ($adjacents * 2)) {
                if ($this->page < 1 + ($adjacents * 2)) {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                        if ($counter == $this->page) {
                            $pagination.= "<li class='active'><span>$counter <span class='sr-only'>(current)</span></span></li>";
                        } else {
                            $pagination.= "<li><a href='" . $path . "$this->instance=$counter" . "$ext'>$counter</a></li>";
                        }
                    }

                    $pagination.= "<li><span style='border: none; background: none; padding: 8px;'>...</span></li>";
                    $pagination.= "<li><a href='" . $path . "$this->instance=$lpm1" . "$ext'>$lpm1</a></li>";
                    $pagination.= "<li><a href='" . $path . "$this->instance=$lastpage" . "$ext'>$lastpage</a></li>";
                } elseif ($lastpage - ($adjacents * 2) > $this->page && $this->page > ($adjacents * 2)) {
                    $pagination.= "<li><a href='" . $path . "$this->instance=1" . "$ext'>1</a></li>";
                    $pagination.= "<li><a href='" . $path . "$this->instance=2" . "$ext'>2</a></li>";
                    $pagination.= "<li><span style='border: none; background: none; padding: 8px;'>...</span></li>";

                    for ($counter = $this->page - $adjacents; $counter <= $this->page + $adjacents; $counter++) {
                        if ($counter == $this->page) {
                            $pagination.= "<li class='active'><span>$counter <span class='sr-only'>(current)</span></span></li>";
                        } else {
                            $pagination.= "<li><a href='" . $path . "$this->instance=$counter" . "$ext'>$counter</a></li>";
                        }
                    }
                    $pagination.= "<li><span style='border: none; background: none; padding: 8px;'>..</span></li>";
                    $pagination.= "<li><a href='" . $path . "$this->instance=$lpm1" . "$ext'>$lpm1</a></li>";
                    $pagination.= "<li><a href='" . $path . "$this->instance=$lastpage" . "$ext'>$lastpage</a></li>";
                } else {
                    $pagination.= "<li><a href='" . $path . "$this->instance=1" . "$ext'>1</a></li>";
                    $pagination.= "<li><a href='" . $path . "$this->instance=2" . "$ext'>2</a></li>";
                    $pagination.= "<li><span style='border: none; background: none; padding: 8px;'>..</span></li>";

                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                        if ($counter == $this->page) {
                            $pagination.= "<li class='active'><span>$counter <span class='sr-only'>(current)</span></span></li>";
                        } else {
                            $pagination.= "<li><a href='" . $path . "$this->instance=$counter" . "$ext'>$counter</a></li>";
                        }
                    }
                }
            }

            if ($this->page < $counter - 1) {
                $pagination.= "<li><a href='" . $path . "$this->instance=$next" . "$ext' aria-label='Next'><span aria-hidden='true'>&raquo;</span></a></li>";
            } else {
                $pagination.= "<li class='disabled'><a href='#' aria-label='Next'><span aria-hidden='true'>&raquo;</span></a></li>";
            }
            $pagination.= "</ul>";
            $pagination.= "</nav>\n";
        }

        return $pagination;
    }

}
