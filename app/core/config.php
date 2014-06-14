<?php

set_exception_handler('logger::exception_handler');
set_error_handler('logger::error_handler');

//set timezone
date_default_timezone_set('Europe/London');

//site address
define('DIR','http://domain.com/');

//Get the current page full url
$c = new Controller();
define('FULLDIR',DIR.implode('/',$c->getUrl()));

//database details ONLY NEEDED IF USING A DATABASE
define('DB_TYPE','mysql');
define('DB_HOST','localhost');
define('DB_NAME','database_name');
define('DB_USER','username');
define('DB_PASS','password');
define('PREFIX','smvc_');

//set prefix for sessions
define('SESSION_PREFIX','smvc_');

//optionall create a constant for the name of the site
define('SITETITLE','Simple MVC Framework v2');

//set the default template
Session::set('template','default');
