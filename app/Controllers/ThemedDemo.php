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
    private $viewFilePath;

    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function beforeFlight()
    {
        $basePath = DS.str_replace(BASEPATH, '', $this->viewsPath);

        $method = $this->method;

        $this->viewFilePath = $basePath.$method.'.php';

        // Leave to Parent's Method the Flight decision.
        return parent::beforeFlight();
    }

    /**
     * Define Index page title and message, then create and return the View instance
     */
    public function index()
    {
        $message = __('Hello, welcome from the welcome controller! <br/>
This content can be changed in <code>{0}</code>', $this->viewFilePath);

        return View::make('welcome')
            ->withTitle(__('Welcome'))
            ->withMessage($message);
    }

    /**
     * Define Subpage page title and message, then create and return the View instance
     */
    public function subPage()
    {
        $viewPath = DS.str_replace(BASEPATH, '', $this->viewsPath).'subpage.php';

        $message = __('Hello, welcome from the welcome controller and subpage method! <br/>
This content can be changed in <code>{0}</code>', $this->viewFilePath);

        return View::make('subpage')
            ->withTitle(__('Subpage'))
            ->withMessage($message);
    }

}
