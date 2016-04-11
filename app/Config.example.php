<?php
/**
 * Config - an example for setting up system settings.
 * When you are done editing, rename this file to 'Config.php'.
 *
 * @author David Carr - dave@daveismyname.com
 * @author Edwin Hoksberg - info@edwinhoksberg.nl
 * @version 3.0
 */

namespace App;

use Core\Language;
use Helpers\Session;
use Helpers\Cookie;

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
         * Define the complete site URL.
         */
        define('SITEURL', 'http://novaframework.dev/');

        /**
         * Define relative base path.
         */
        define('DIR', '/');

        /**
         * Set the Application Router.
         */
        // Default Routing
        define('APPROUTER', '\Core\Router');
        // Classic Routing
        // define('APPROUTER', '\Core\ClassicRouter');

        /**
         * Set default controller and method for legacy calls.
         */
        define('DEFAULT_CONTROLLER', 'Welcome');
        define('DEFAULT_METHOD', 'index');

        /**
         * Set the default template.
         */
        define('TEMPLATE', 'Default');

        /**
         * Set a default language.
         */
        define('LANGUAGE_CODE', 'En');

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
        define('PREFIX', 'nova_');

        /**
         * Set prefix for sessions.
         */
        define('SESSION_PREFIX', 'nova_');

        /**
         * Optional create a constant for the name of the site.
         */
        define('SITETITLE', 'Nova V3.0');

        /**
         * Optional set a site email address.
         */
        // define('SITEEMAIL', 'email@domain.com');

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

        /**
         * Setup the current Language.
         */
        self::setupLanguage();
    }

    protected static function setupLanguage()
    {
        if (Session::exists('language')) {
            // The Language was already set; nothing to do.
            return;
        } else if(Cookie::exists(PREFIX .'language')) {
            $cookie = Cookie::get(PREFIX .'language');

            if (preg_match ('/[a-z]/', $cookie) && in_array($cookie, Language::$codes)) {
                Session::set('language', ucfirst($cookie));
            }
        }
    }
}
