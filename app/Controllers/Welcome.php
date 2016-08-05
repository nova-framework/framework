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
     * Define Index page title and load template files.
     */
    public function index()
    {
        $data = array(
            'welcomeMessage' => __('Hello, welcome from the welcome controller! <br/>
this content can be changed in <code>/app/Views/Welcome/Welcome.php</code>')
        );

        return View::make('Welcome/Welcome')
            ->shares('title', __('Welcome'))
            ->with($data);
    }

    /**
     * The New Style Rendering - create and return a proper View instance.
     */
    public function subPage()
    {
        $message = __('Hello, welcome from the welcome controller and subpage method! <br/>
This content can be changed in <code>/app/Views/Welcome/SubPage.php</code>');

        return $this->getView()
            ->shares('title', __('Subpage'))
            ->withWelcomeMessage($message);
    }

}
