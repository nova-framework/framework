<?php
/**
 * Error class - calls a 404 page.
 *
 * @author David Carr - dave@daveismyname.com
 *
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */
namespace Core;

/**
 * Error class to generate 404 pages.
 */
class Error extends Controller
{
    /**
     * $error holder.
     *
     * @var string
     */
    private $error = null;

    /**
     * Save error to $this->error.
     *
     * @param string $error
     */
    public function __construct($error)
    {
        parent::__construct();
        $this->error = $error;
    }

    /**
     * Load a 404 page with the error message.
     */
    public function index()
    {
        header('HTTP/1.0 404 Not Found');

        $data['title'] = '404';
        $data['error'] = $this->error;

        View::renderTemplate('header', $data);
        View::render('error/404', $data);
        View::renderTemplate('footer', $data);
    }

    /**
     * Display errors.
     *
     * @param array  $error an error of errors
     * @param string $class name of class to apply to div
     *
     * @return string return the errors inside divs
     */
    public static function display($error, $class = 'alert alert-danger')
    {
        if (is_array($error)) {
            foreach ($error as $error) {
                $row .= "<div class='$class'>$error</div>";
            }

            return $row;
        } else {
            if (isset($error)) {
                return "<div class='$class'>$error</div>";
            }
        }
    }
}
