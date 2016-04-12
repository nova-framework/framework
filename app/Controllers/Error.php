<?php
namespace App\Controllers;

use Core\Controller;
use Core\View;

/**
 * Error controller to generate 404 pages.
 */
class Error extends Controller
{
    /**
     * Call the parent construct.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Load a 404 page with the Error message.
     */
    public function index($error)
    {
        $data['title'] = '404';
        $data['error'] = $error;

        View::addHeader("HTTP/1.0 404 Not Found");

        View::renderTemplate('header', $data);
        View::render('Error/404', $data);
        View::renderTemplate('footer', $data);
    }
}
