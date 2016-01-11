<?php
/**
 * Logger class - Custom errors
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Nova;

use Nova\Helpers\PhpMailer as Mailer;
use Nova\Config;

/**
 * Record and email/display errors or a custom error message.
 */
class Logger
{
    /**
    * Determins if error should be emailed to SITE_EMAIL defined in app/Core/Config.php.
    *
    * @var boolean
    */
    private static $emailError = false;

    /**
    * Clear the errorlog.
    *
    * @var boolean
    */
    private static $clear = false;

    /**
    * show the error.
    *
    * @var boolean
    */
    private static $display = false;

    /**
    * Path to error file.
    *
    * @var string
    */
    public static $errorFile = '/storage/logs/error.log';

    /**
    * store errors for output.
    *
    * @var string
    */
    public static $error;


    public static function initialize()
    {
        $options = Config::get('logger');

        if ($options === null) {
            return;
        }

        self::$display = $options['display_errors'];
    }

    /**
    * In the event of an error show this message.
    */
    public static function customErrorMsg()
    {
        if (self::$display) {
            echo '<pre>'.self::$error.'</pre>';
        } else {
            echo "<p>" .__d('system', 'An error occurred. The error has been reported.') ."</p>";
            exit;
        }

    }

    /**
    * Saved the exception and calls customer error function.
    *
    * @param  \Exception $e
    */
    public static function exceptionHandler($e)
    {
        self::newMessage($e);
    }

    /**
     * Saves error message from exception.
     *
     * @param  number $number  error number
     * @param  string  $message the error
     * @param  string  $file    file originated from
     * @param  number $line    line number
     *
     * @return int
     */
    public static function errorHandler($number, $message, $file, $line)
    {
        $msg = "$message in $file on line $line";

        if (($number !== E_NOTICE) && ($number < 2048)) {
            self::errorMessage($msg);
            self::$error = $msg;
            self::customErrorMsg();
        }

        return 0;
    }

    /**
     * New exception.
     *
     * @param  \Exception $exception
     * @internal param bool $printError show error or not
     * @internal param bool $clear clear the errorlog
     * @internal param string $errorFile file to save to
     */
    public static function newMessage($exception)
    {
        $message = $exception->getMessage();
        $code = $exception->getCode();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();
        $trace = str_replace(DB_PASS, '********', $trace);
        $date = date('M d, Y G:iA');
        $rootpath = dirname(__DIR__);

        $logMessage = "Exception information:\n
           Date: {$date}\n
           Message: {$message}\n
           Code: {$code}\n
           File: {$file}\n
           Line: {$line}\n
           Stack trace:\n
           {$trace}\n
           ---------\n\n";

        if (is_file($rootpath.self::$errorFile) === false) {
            file_put_contents($rootpath.self::$errorFile, '');
        }

        if (self::$clear) {
            $f = fopen($rootpath.self::$errorFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }
        }

        // Append
        file_put_contents($rootpath.self::$errorFile, $logMessage, FILE_APPEND);

        self::$error = $logMessage;
        self::customErrorMsg();

        //send email
        self::sendEmail($logMessage);
    }

    /**
     * Custom error.
     *
     * @param  string $error the error
     * @internal param bool $printError display error
     * @internal param string $errorFile file to save to
     */
    public static function errorMessage($error)
    {
        $date = date('Y-m-d G:iA');
        $logMessage = "$date - $error\n\n";
        $rootpath = dirname(__DIR__);

        if (is_file($rootpath.self::$errorFile) === false) {
            file_put_contents($rootpath.self::$errorFile, '');
        }

        if (self::$clear) {
            $f = fopen($rootpath.self::$errorFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }

            $content = null;
        } else {
            // Append
            file_put_contents($rootpath.self::$errorFile, $logMessage, FILE_APPEND);
        }

        /** send email */
        self::sendEmail($logMessage);
    }

    /**
     * Send Email upon error.
     *
     * @param  string $message holds the error to send
     */
    public static function sendEmail($message)
    {
        if (self::$emailError == true) {
            $mail = new Mailer();

            $mail->setFrom(SITE_EMAIL);
            $mail->addAddress(SITE_EMAIL);
            $mail->Subject = 'New error on '.SITE_TITLE;
            $mail->Body = $message;

            $mail->send();
        }
    }
}
