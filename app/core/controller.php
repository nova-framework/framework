<?php namespace core;
use core\config   as Config,
	core\language as Language,
    core\error    as Error;

/*
 * controller - base controller
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 2.1
 * @date June 27, 2014
 */
class Controller {

	/**
	 * on run make an instance of the config class and view class
	 */
	public function __construct(){
		//initialise the language object
		$this->language = new Language();
	}

}
