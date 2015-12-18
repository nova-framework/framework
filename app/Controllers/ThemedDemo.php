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
 * Sample Themed Controller with its typical usage.
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
        $method = $this->method();

        $viewsPath = str_replace(BASEPATH, '', $this->viewsPath());

        $this->viewFilePath = $viewsPath.$method.'.php';

        // Leave to parent's method the Flight decisions.
        return parent::beforeFlight();
    }

    public function afterFlight($result)
    {
        // Do some processing there, even deciding to stop the Flight, if case.

        // Leave to parent's method the Flight decisions.
        return parent::afterFlight($result);
    }

    /**
     * CakePHP style - Define Welcome page message and set the Controller's variables.
     */
    public function welcome()
    {
        $message = __('Hello, welcome from the welcome controller! <br/>
This content can be changed in <code>{0}</code>', $this->viewFilePath);

       // Setup the View variables.
        $this->title(__('Welcome'));

        $this->set('message', $message);
    }

    /**
     * Laravel style - Define Subpage page message, then create and return the View instance
     */
    public function subPage()
    {
        $message = __('Hello, welcome from the welcome controller and subpage method! <br/>
This content can be changed in <code>{0}</code>', $this->viewFilePath);

        return View::make('subpage')
            ->withTitle(__('Subpage'))
            ->withMessage($message);
    }

    /**
     * Gagnam style - TBD
     */

}
