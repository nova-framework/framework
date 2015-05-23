<?php
namespace Core;

use Core\View;
use Core\Language;

/*
 * controller - base controller
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */

abstract class Controller
{
    /**
     * view variable to use the view class
     * @var string
     */
    public $view;
    public $language;

    /**
     * on run make an instance of the config class and view class
     */
    public function __construct()
    {
        //initialise the views object
        $this->view = new View();

        //initialise the language object
        $this->language = new Language();
    }
}
