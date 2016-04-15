<?php
/**
 * Controller - base controller
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */

namespace Core;

use Core\Language;

/**
 * Core controller, all other controllers extend this base controller.
 */
abstract class Controller
{
    /**
     * Language variable to use the languages class.
     *
     * @var string
     */
    public $language;

    /**
     * On run make an instance of the config class and view class.
     */
    public function __construct()
    {
        /** Initialise the Language object */
        $this->language = new Language();
    }

    /**
     * Method automatically invoked after the current Action, when it not return a
     * null or boolean value. This Method is supposed to be overriden for using it.
     *
     * Note that the Action's returned value is passed to this Method as parameter.
     */
    public function after($data)
    {
    }
}
