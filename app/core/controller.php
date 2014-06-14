<?php

class Controller {

	public $view;

	public function __construct(){
		//initialise the views object
		$this->view = new view();

		//call the getUrl function to collect the url structure
		$this->getUrl();
	}

	//return an array of url segments
	public function getUrl(){
		$url = isset($_SERVER['REQUEST_URI']) ? rtrim($_SERVER['REQUEST_URI'], '/') : NULL;
        	$url = filter_var($url, FILTER_SANITIZE_URL);
		$url = explode('/',$url);
	 	$url = array_filter($url);
        	return $url;
    	}

	//Display an error page if nothing exists
	protected function _error($error) {
		require 'app/core/error.php';
		$this->_controller = new Error($error);
	    	$this->_controller->index();
	    	die;
	}


	//function to load model on request
	public function loadModel($name){

		$modelpath = strtolower('app/models/'.$name.'.php');

		//try to load and instantiate model		
		if(file_exists($modelpath)){
			
			require_once $modelpath;

			//break name into sections based on a / 
			$parts = explode('/',$name);

			//use last part of array
			$modelName = ucwords(end($parts));

			//instantiate object
			$model = new $modelName();

			//return object to controller
			return $model;

		} else {
			$this->_error("Model does not exist: ".$modelpath);
			return false;
		}

	}

	//function to load model on request
	public function loadHelper($name, $type = 'noneStatic'){

		$helperpath = strtolower('app/helpers/'.$name.'.php');

		//try to load and instantiate helper		
		if(file_exists($helperpath)){
			
			require_once $helperpath;

			//break name into sections based on a / 
			$parts = explode('/',$name);

			//if object is not static then instantiate it.
			if($type == 'noneStatic'){

				//use last part of array
				$helperName = ucwords(end($parts));

				//instantiate object
				$helper = new $helperName();

				//return object to controller
				return $helper;

			}

		} else {
			$this->_error("Helper does not exist: ".$modelpath);
			return false;
		}

	}

}
