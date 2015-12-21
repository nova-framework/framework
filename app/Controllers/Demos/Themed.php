<?php
/**
 * Welcome controller
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 17th, 2015
 */

namespace App\Controllers\Demos;

use Nova\Core\View;
use App\Core\ThemedController;

/**
 * Sample Themed Controller with its typical usage.
 */
class Themed extends ThemedController
{
    private $basePath;


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * CakePHP style - Define Welcome page message and set the Controller's variables.
     */
    public function welcome()
    {
        $viewName = $this->method();

        $filePath = $this->basePath . $viewName . '.php';

        $message = __('Hello, welcome from the welcome controller! <br/>
This content can be changed in <code>{0}</code>', $filePath);

        // Setup the View variables.
        $this->title(__('Welcome'));

        $this->set('message', $message);
    }

    /**
     * Laravel style - Define Subpage page message, then create and return the View instance
     */
    public function subPage()
    {
        $viewName = 'subpage';

        $filePath = $this->basePath . $viewName . '.php';

        $message = __('Hello, welcome from the welcome controller and subpage method! <br/>
This content can be changed in <code>{0}</code>', $filePath);

        return View::make($viewName)
            ->withTitle(__('Subpage'))
            ->withMessage($message);
    }

    protected function beforeFlight()
    {
        $this->basePath = str_replace(BASEPATH, '', $this->viewsPath());

        // Leave to parent's method the Flight decisions.
        return parent::beforeFlight();
    }

    protected function afterFlight($result)
    {
        // Do some processing there, even deciding to stop the Flight, if case.

        // Leave to parent's method the Flight decisions.
        return parent::afterFlight($result);
    }

    /**
     * Gangnam style - TBD
     */

}
