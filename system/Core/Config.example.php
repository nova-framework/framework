<?php
/**
 * Config - an example for setting up system settings.
 * When you are done editing, rename this file to 'Config.php'.
 *
 * @author David Carr - dave@daveismyname.com
 * @author Edwin Hoksberg - info@edwinhoksberg.nl
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Smvc\Core;

use Smvc\Helpers\Session;

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
         * Set the Application Router.
         */
        // Default Routing
        define('APPROUTER', '\Smvc\Core\Router');
        // Classic Routing
        //define('APPROUTER', '\App\Core\ClassicRouter');

        /**
         * Set default controller and method for legacy calls.
         */
        define('DEFAULT_CONTROLLER', 'Welcome');
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
        define('SITETITLE', 'V3.0');

        /**
         * Optional set a site email address.
         */
        // define('SITEEMAIL', 'email@domain.com');

        /**
         * Email (PHPMailer) configuration
         */
        define('MAILER_CHARSET', 'iso-8859-1');
        define('MAILER_FROM_NAME', 'SMVC Website');
        define('MAILER_FROM_EMAIL', 'smvc@localhost');
        define('MAILER_MAILER', 'mail'); // Could be 'mail', 'sendmail' or 'smtp'

        /** Only when using smtp as mailer: */
        define('MAILER_SMTP_HOST', 'localhost');
        define('MAILER_SMTP_PORT', 25);
        define('MAILER_SMTP_SECURE', ''); // Options: '', 'ssl' or 'tls'
        define('MAILER_SMTP_AUTH', false); // Use SMTPAuth, (false or true)
        define('MAILER_SMTP_USER', ''); // Only when using SMTPAuth
        define('MAILER_SMTP_PASS', ''); // Only when using SMTPAuth
        define('MAILER_SMTP_AUTHTYPE', ''); // Options are LOGIN (default), PLAIN, NTLM, CRAM-MD5. Blank when not use SMTPAuth.


        /**
         * Turn on custom error handling.
         */
        set_exception_handler('Smvc\Core\Logger::ExceptionHandler');
        set_error_handler('Smvc\Core\Logger::ErrorHandler');

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
