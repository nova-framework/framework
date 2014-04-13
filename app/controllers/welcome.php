<?php

class Welcome extends Controller{

	public function __construct(){
		parent::__construct();
	}

	public function index($request = null){

		$data['title'] = 'Welcome';

		$this->view->rendertemplate('header',$data);
		$this->view->render('welcome/welcome',$data);
		$this->view->rendertemplate('footer',$data);
	}
	
}