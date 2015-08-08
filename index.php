<?php
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    echo "<h1>Please install via composer.json</h1>";
    echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
    echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
    exit;
}

if (!is_readable('app/Core/Config.php')) {
    die('No Config.php found, configure and rename Config.example.php to Config.php in app/Core.');
}

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
if ($_SERVER ['HTTP_HOST'] == 'www.localhost.com') {
	define ( 'ENVIRONMENT', 'production' );
} else {
	define ( 'ENVIRONMENT', 'development' );
}

/*
 * ---------------------------------------------------------------
 * INTERNET CONNECTION
 * ---------------------------------------------------------------
 */
define ( 'APPSTATE', 'online' );
if (defined ( 'ENVIRONMENT' )) {
	switch (ENVIRONMENT) {
		case 'development' :
			$connected = @fsockopen ( "www.google.com", 80 );
			if ($connected) {
				define ( 'APPSTATE', 'online' );
				fclose ( $connected );
			} else {
				define ( 'APPSTATE', 'offline' );
			}
			break;
	}
}

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but production will hide them.
 */

if (defined('ENVIRONMENT')) {
    switch (ENVIRONMENT) {
        case 'development':
            error_reporting(E_ALL);
            break;
        case 'production':
            error_reporting(0);
            break;
        default:
            exit('The application environment is not set correctly.');
    }

}

//initiate config
new Core\Config();

// initiate routing
require 'routing.php';

