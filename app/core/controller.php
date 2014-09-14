<?php namespace core;
use core\config as Config,
    core\view as View,
    core\error as Error;

/*
 * controller - base controller
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 2.1
 * @date June 27, 2014
 */
class Controller {

	/**
	 * view variable to use the view class
	 * @var string
	 */
	public $view;

	/**
	 * on run make an instance of the config class and view class 
	 */
	public function __construct(){
<<<<<<< HEAD
		
=======

>>>>>>> upstream/master
		//initialise the views object
		$this->view = new view();
	}

}
