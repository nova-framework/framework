<?php namespace controllers;

use core\view;

/*
 * Welcome controller
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 2.1
 * @date June 27, 2014
 */
class Test extends \core\controller{

	

	/**
	 * Define Index page title and load template files
	 */
	public function index ($hello = null, $abc = null) {
		var_dump(func_get_args(), $_GET);
	}

}
