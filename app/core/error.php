<?php namespace Core;
use core\controller as Controller;
use core\view as View;

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
		
		$data['title'] = '404';
		$data['error'] = $this->_error;
		
		View::rendertemplate('header',$data);
		View::render('error/404',$data);
		View::rendertemplate('footer',$data);
		
	}

}
