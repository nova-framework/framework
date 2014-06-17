<?php

class Welcome extends Controller{

	public function __construct(){
		parent::__construct();
	}

	public function index($request = null){

		$data['title'] = 'Welcome';

		View::rendertemplate('header',$data);
		View::render('welcome/welcome',$data);
		View::rendertemplate('footer',$data);
	}

}
