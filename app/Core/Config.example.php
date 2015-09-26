<?php
/**
 * Config - an example for setting up system settings.
 * When you are done editing, rename this file to 'config.php'.
 *
 * @author David Carr - dave@daveismyname.com
 * @author Edwin Hoksberg - info@edwinhoksberg.nl
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Core;

use Helpers\Session;

/**
 * Configuration constants and options.
 */
class Config
{
    /**
     * Executed as soon as the framework runs.
     */
    public function __construct()
    {
        /**
         * Turn on output buffering.
         */
        ob_start();

        /**
         * Define relative base path.
         */
        define('DIR', '/');

        /**
         * Set default controller and method for legacy calls.
         */
        define('DEFAULT_CONTROLLER', 'welcome');
        define('DEFAULT_METHOD', 'index');

        /**
         * Set the default template.
         */
        define('TEMPLATE', 'default');

        /**
         * Set a default language.
         */
        define('LANGUAGE_CODE', 'en');

        //database details ONLY NEEDED IF USING A DATABASE

        /**
         * Database engine default is mysql.
         */
        define('DB_TYPE', 'mysql');

        /**
         * Database host default is localhost.
         */
        define('DB_HOST', 'localhost');

        /**
         * Database name.
         */
        define('DB_NAME', 'dbname');

        /**
         * Database username.
         */
        define('DB_USER', 'root');

        /**
         * Database password.
         */
        define('DB_PASS', 'password');

        /**
         * PREFER to be used in database calls default is smvc_
         */
        define('PREFIX', 'smvc_');

        /**
         * Set prefix for sessions.
         */
        define('SESSION_PREFIX', 'smvc_');

        /**
         * Optional create a constant for the name of the site.
         */
        define('SITETITLE', 'V2.2');

        /**
         * Optionall set a site email address.
         */
        //define('SITEEMAIL', '');

        /**
         * Turn on custom error handling.
         */
        set_exception_handler('Core\Logger::ExceptionHandler');
        set_error_handler('Core\Logger::ErrorHandler');

        /**
         * Set timezone.
         */
        date_default_timezone_set('Europe/London');

        /**
         * Start sessions.
         */
        Session::init();
    }
}
