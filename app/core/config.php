<?php namespace core;
/*
 * config - setup system wide settings
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 2.1
 * @date June 27, 2014
 */
class Config {

	public function __construct(){

		//turn on output buffering
		ob_start();

		//site address
		define('DIR','http://domain.com/');
		
		//set default controller and method for legacy calls
		define('DEFAULT_CONTROLLER', 'index');
    		define('DEFAULT_METHOD', 'index');

		//database details ONLY NEEDED IF USING A DATABASE
		define('DB_TYPE','mysql');
		define('DB_HOST','localhost');
		define('DB_NAME','dbname');
		define('DB_USER','username');
		define('DB_PASS','password');
		define('PREFIX','smvc_');

		//set prefix for sessions
		define('SESSION_PREFIX','smvc_');

		//optionall create a constant for the name of the site
		define('SITETITLE','V2.1');
		
		// Default controller and method
		define('DEFAULT_CONTROLLER', 'welcome');
		define('DEFAULT_METHOD'    , 'index');
		
		//turn on custom error handling
		set_exception_handler('core\logger::exception_handler');
		set_error_handler('core\logger::error_handler');
		
		//set timezone
		date_default_timezone_set('Europe/London');
		
		//start sessions
		\helpers\session::init();

		//set the default template
		\helpers\session::set('template','default');
		
	}

}
