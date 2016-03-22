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

use Nova\Core\View;
use App\Core\BaseController;

/**
 * Sample controller showing a construct and 2 methods and their typical usage.
 */
class Welcome extends BaseController
{
    private $basePath;


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function before()
    {
        $this->basePath = str_replace(BASEPATH, '', $this->viewsPath());

        // Leave to parent's method the Flight decisions.
        return parent::before();
    }

    protected function after($result)
    {
        // Do some processing there, even deciding to stop the Flight, if case.

        // Leave to parent's method the Flight decisions.
        return parent::after($result);
    }

    /**
     * Define Welcome page title and load template files
     */
    public function welcome()
    {
        $viewName = $this->method();

        $filePath = $this->basePath .$viewName .'.php';

        $message = __('Hello, welcome from the welcome controller! <br/>
This content can be changed in <code>{0}</code>', $filePath);

        // Setup the View variables.
        $this->title(__('Welcome'));

        $this->set('welcome_message', $message);
    }

    /**
     * Define Subpage page title and load template files
     */
    public function subPage()
    {
        $viewName = $this->method();

        $filePath = $this->basePath .$viewName .'.php';

        $message = __('Hello, welcome from the welcome controller and subpage method! <br/>
This content can be changed in <code>{0}</code>', $filePath);

        // Setup the View variables.
        $this->title(__('Subpage'));

        $this->set('welcome_message', $message);
    }
}
