<?php namespace core;
/*
 * config - setup system wide settings
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 2.1
 * @date June 27, 2014
 */
 
use \helpers\session as Session,
\core\logger as Logger;
 
class Config {

	public function __construct(){

		//turn on output buffering
		ob_start();

		//turn on custom error handling
		set_exception_handler('Logger::exception_handler');
		set_error_handler('Logger::error_handler');

		//set timezone
		date_default_timezone_set('Europe/London');

		//site address
		define('DIR','http://domain.com/');

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

		//start sessions
		Session::init();

		//set the default template
		Session::set('template','default');
		
	}

}
