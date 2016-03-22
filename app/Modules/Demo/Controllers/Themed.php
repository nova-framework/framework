<?php
/**
 * Welcome controller
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 17th, 2015
 */

namespace App\Modules\Demo\Controllers;

use Nova\Core\View;
use App\Modules\Demo\Core\BaseController;

/**
 * Sample Themed Controller with its typical usage.
 */
class Themed extends BaseController
{
    private $basePath;

    protected $useLayout = true;

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

    /**
     * CakePHP style - Define Welcome page message and set the Controller's variables.
     */
    public function welcome()
    {
        $viewName = $this->method();

        $filePath = $this->basePath .$viewName .'.php';

        $message = __d('demo', 'Hello, welcome from the welcome controller! <br/>
This content can be changed in <code>{0}</code>', $filePath);

       // Setup the View variables.
        $this->title(__d('demo', 'Welcome'));

        $this->set('message', $message);
    }

    /**
     * Laravel style - Define Subpage page message, then create and return the View instance
     */
    public function subPage()
    {
        $viewName = $this->method();

        $filePath = $this->basePath .$viewName .'.php';

        $message = __d('demo', 'Hello, welcome from the welcome controller and subpage method! <br/>
This content can be changed in <code>{0}</code>', $filePath);

        //
        View::share('title', __d('demo', 'Subpage'));

        return View::make($viewName)->withMessage($message);
    }

    /**
     * Gangnam style - TBD
     */
}
