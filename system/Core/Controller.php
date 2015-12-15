<?php
/**
 * Controller - base controller
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Smvc\Core;

use Smvc\Core\View;
use Smvc\Core\Language;

/**
 * Core controller, all other controllers extend this base controller.
 */
abstract class Controller
{
    /**
     * View variable to use the view class.
     *
     * @var string
     */
    public $view;

    /**
     * On run make an instance of the config class and view class.
     */
    public function __construct()
    {
        /** initialise the language object */
        $this->view = new View();
    }
}
