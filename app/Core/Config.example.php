<?php
namespace Core;

use Helpers\Session;

/*
 * config - an example for setting up system settings
 * When you are done editing, rename this file to 'config.php'
 *
 * @author David Carr - dave@simplemvcframework.com
 * @author Edwin Hoksberg - info@edwinhoksberg.nl
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */
class Config
{
    public function __construct()
    {
        //turn on output buffering
        ob_start();

        //site address
        define('DIR', 'http://domain.com');

        //set default controller and method for legacy calls
        define('DEFAULT_CONTROLLER', 'welcome');
        define('DEFAULT_METHOD', 'index');

        //set the default template
        define('TEMPLATE', 'default');

        //set a default language
        define('LANGUAGE_CODE', 'en');

        //database details ONLY NEEDED IF USING A DATABASE
        define('DB_TYPE', 'mysql');
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'dbname');
        define('DB_USER', 'root');
        define('DB_PASS', 'password');
        define('PREFIX', 'smvc_');

        //set prefix for sessions
        define('SESSION_PREFIX', 'smvc_');

        //optionall create a constant for the name of the site
        define('SITETITLE', 'V2.2');

        //optionall set a site email address
        //Email do administrador para notificação de erros no sistema
        define('SITEEMAIL', 'mail@domain.com.br');

        define('MAIL_SMTP_AUTH', true); // // Enable SMTP authentication
        define('MAIL_IS_HTML', true);  // Set email format to HTML
        define('MAIL_CHARSET', 'UTF-8');
        define('MAIL_SMTP_SECURE', 'tls'); // Enable TLS encryption, `ssl` also accepted
        define('MAIL_HOST', 'smtp.gmail.com'); //Servidor de envio
        define('MAIL_PORT', '587'); //Porta de envio
        define('MAIL_USER', 'mail@gmail.com'); //Login do email de envio
        define('MAIL_PASS', 'secret'); //Senha

        //turn on custom error handling
        set_exception_handler('Core\Logger::ExceptionHandler');
        set_error_handler('Core\Logger::ErrorHandler');

        //set timezone
        date_default_timezone_set('Europe/London');

        //start sessions
        Session::init();
    }
}
