<?php
/**
 * Welcome controller
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace App\Controllers;

use Smvc\Core\View;
use Smvc\Core\Controller;

/**
 * Sample controller showing a construct and 2 methods and their typical usage.
 */
class Welcome extends Controller
{
    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Define Index page title and load template files
     */
    public function index()
    {
        $data['title'] = __('Welcome');
        $data['welcome_message'] = __('Hello, welcome from the welcome controller! <br/>
This content can be changed in <code>/app/views/welcome/welcome.php</code>');

        View::renderTemplate('header', $data);
        View::render('welcome', $data);
        View::renderTemplate('footer', $data);
    }

    /**
     * Define Subpage page title and load template files
     */
    public function subPage()
    {
        $data['title'] = __('Subpage');
        $data['welcome_message'] = __('Hello, welcome from the welcome controller and subpage method! <br/>
This content can be changed in <code>/app/views/welcome/subpage.php</code>');

        View::renderTemplate('header', $data);
        View::render('subpage', $data);
        View::renderTemplate('footer', $data);
    }
}
