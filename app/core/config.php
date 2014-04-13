<?php

set_exception_handler('logger::exception_handler');
set_error_handler('logger::error_handler');

//set timezone
date_default_timezone_set('Europe/London');

//site address
define('DIR','http://dev.webdesignhull.com/top/shop/');

//database details ONLY NEEDED IF USING A DATABASE
define('DB_TYPE','mysql');
define('DB_HOST','localhost');
define('DB_NAME','top');
define('DB_USER','top');
define('DB_PASS','t0pdigital14');
define('PREFIX','topcms_');

//set prefix for sessions
define('SESSION_PREFIX','topcms_');

//optionall create a constant for the name of the site
define('SITETITLE','The One Point Shop');

//set the default template
Session::set('template','default');