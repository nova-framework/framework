<?php
/**
 * Welcome controller
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */

namespace App\Controllers;

use Core\View;
use Core\Controller;

use Language;
use Router;
use Session;

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
     * Define Index page title and load template files.
     */
    public function index()
    {
        $data['welcomeMessage'] = __('Hello, welcome from the welcome controller! <br/>
this content can be changed in <code>/app/Views/Welcome/Welcome.php</code>');

        return View::make('Welcome/Welcome', $data)->shares('title', __('Welcome'));
    }

    /**
     * The New Style Rendering - create and return a proper View instance.
     */
    public function subPage()
    {
        return $this->getView()
            ->shares('title', __('Subpage'))
            ->withWelcomeMessage(__('Hello, welcome from the welcome controller and subpage method! <br/>
This content can be changed in <code>/app/Views/Welcome/SubPage.php</code>'));
    }

}
