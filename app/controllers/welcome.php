<?php namespace controllers;
use core\view as View;

class Welcome extends \core\controller{

	public function __construct(){
		parent::__construct();
	}

	public function index(){	

		$data['title'] = 'Welcome';

		View::rendertemplate('header',$data);
		View::render('welcome/welcome',$data);
		View::rendertemplate('footer',$data);
	}
	
}