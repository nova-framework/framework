<?php namespace core;
use core\controller,
    core\view;

/*
 * error class - calls a 404 page
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 2.1
 * @date June 27, 2014
 */
class Error extends Controller {

	/**
	 * $error holder
	 * @var string
	 */
	private $_error = null; 

	/**
	 * save error to $this->_error
	 * @param string $error 
	 */
	public function __construct($error){
		parent::__construct();
		$this->_error = $error;
	}

	/**
	 * load a 404 page with the error message
	 */
	public function index(){

		header("HTTP/1.0 404 Not Found");
		
		$data['title'] = '404';
		$data['error'] = $this->_error;
		
		View::rendertemplate('header',$data);
		View::render('error/404',$data);
		View::rendertemplate('footer',$data);
		
	}

	/**
	 * display errors
	 * @param  array  $error an error of errors
	 * @param  string $class name of class to apply to div
	 * @return string        return the errors inside divs
	 */
	public static function display($error, $class = 'alert alert-danger'){
		if (is_array($error)){

			foreach($error as $error){
				$row.= "<div class='$class'>$error</div>";
			}
			return $row;

		} else {

			if(isset($error)){
				return "<div class='$class'>$error</div>";
			}

		}
	}

}
