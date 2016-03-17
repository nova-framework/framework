<?php
/**
 * Error class - calls a 404 page.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date Mar 17, 2016
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
    protected $useLayout = true;


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

        //
        View::share('title', __('Error 404'));

        return View::make('error404')->withError($error);
    }
}
