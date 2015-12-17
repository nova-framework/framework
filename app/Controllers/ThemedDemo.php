<?php
/**
 * Welcome controller
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 17th, 2015
 */

namespace App\Controllers;

use Smvc\Core\View;
use App\Core\ThemedController;

/**
 * Sample Themed Controller showing a construct and 2 methods and their typical usage.
 */
class ThemedDemo extends ThemedController
{
    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Define Index page title and message, then create and return the View instance
     */
    public function index()
    {
        $message = __('Hello, welcome from the welcome controller! <br/>
This content can be changed in <code>/app/views/welcome/welcome.php</code>');

        return View::make('welcome')
            ->withTitle(__('Welcome'))
            ->withMessage($message);
    }

    /**
     * Define Subpage page title and message, then create and return the View instance
     */
    public function subPage()
    {
        $message = __('Hello, welcome from the welcome controller and subpage method! <br/>
This content can be changed in <code>/app/views/welcome/subpage.php</code>');

        return View::make('subpage')
            ->withTitle(__('Subpage'))
            ->withMessage($message);
    }

}
