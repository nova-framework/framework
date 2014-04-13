<?php 

class Error extends Controller {

	private $_error = null; 

	public function __construct($error){
		parent::__construct();
		$this->_error = $error;
	}

	public function index(){
		
		$data['title'] = '404';
		$data['error'] = $this->_error;
		
		$this->view->rendertemplate('header',$data);
		$this->view->render('error/404',$data);
		$this->view->rendertemplate('footer',$data);
		
	}

}
