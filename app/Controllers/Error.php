<?php
/**
 * Error class - calls a 404 page.
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace App\Controllers;

use App\Core\BaseController;
use Nova\Core\View;
use Nova\Net\Response;

/**
 * Error class to generate 404 pages.
 */
class Error extends BaseController
{
    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function beforeFlight()
    {
        // Leave to parent's method the Flight decisions.
        return parent::beforeFlight();
    }

    protected function afterFlight($result)
    {
        // Leave to parent's method the Flight decisions.
        return parent::afterFlight($result);
    }

    /**
     * Load a 404 page with the error message.
     *
     * @param mixed $error
     */
    public function error404($error = null)
    {
        Response::addHeader("HTTP/1.0 404 Not Found");

        return View::make('error404')
            ->withTitle('404')
            ->withError($error);
    }
}
