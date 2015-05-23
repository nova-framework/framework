<?php
namespace Core;

use Core\Controller;
use Core\View;

/*
 * error class - calls a 404 page
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */
class Error extends Controller
{
    /**
     * $error holder
     * @var string
     */
    private $error = null;

    /**
     * save error to $this->_error
     * @param string $error
     */
    public function __construct($error)
    {
        parent::__construct();
        $this->error = $error;
    }

    /**
     * load a 404 page with the error message
     */
    public function index()
    {
        header("HTTP/1.0 404 Not Found");

        $data['title'] = '404';
        $data['error'] = $this->error;

        View::renderTemplate('header', $data);
        View::render('error/404', $data);
        View::renderTemplate('footer', $data);
    }

    /**
     * display errors
     * @param  array  $error an error of errors
     * @param  string $class name of class to apply to div
     * @return string        return the errors inside divs
     */
    public static function display($error, $class = 'alert alert-danger')
    {
        if (is_array($error)) {
            foreach ($error as $error) {
                $row.= "<div class='$class'>$error</div>";
            }
            return $row;
        } else {
            if (isset($error)) {
                return "<div class='$class'>$error</div>";
            }
        }
    }
}
