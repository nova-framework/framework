<?php namespace Core;
use core\controller as Controller;
use core\view as View;

class Error extends Controller {

	private $_error = null; 

	public function __construct($error){
		parent::__construct();
		$this->_error = $error;
	}

	public function index(){
		
		$data['title'] = '404';
		$data['error'] = $this->_error;
		
		View::rendertemplate('header',$data);
		View::render('error/404',$data);
		View::rendertemplate('footer',$data);
		
	}

}
