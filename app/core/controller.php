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
		
		//initialise the views object
		$this->view = new view();
	}

	/**
	 * Display an error page if nothing exists
	 * @param	string $error
	 */
	protected function _error($error) {
		require 'app/core/error.php';
		$this->_controller = new error($error);
	    	$this->_controller->index();
	    	die;
	}

}
