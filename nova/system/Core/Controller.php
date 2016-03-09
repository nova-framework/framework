<?php
/**
 * Controller - base controller
 *
 * @author David Carr - dave@daveismyname.com
 * @version 3.0
 * @date June 27, 2014
 * @date updated March 9th, 2016
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
        /** initialise the language object */
        $this->language = new Language();
    }
}
