<?php namespace core;
use core\config as Config;
use core\view as View;
use core\error as Error;

class Controller {

	public $view;

	public function __construct(){	
		//initialise the config object
		new config();
		//initialise the views object
		$this->view = new view();
	}

	//Display an error page if nothing exists
	protected function _error($error) {
		require 'app/core/error.php';
		$this->_controller = new error($error);
	    	$this->_controller->index();
	    	die;
	}

}
