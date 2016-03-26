<?php
/**
 *occurred class - Custom errors
 *
 * @author David Carr - dave@daveismyname.com
 * @version 3.0
 */

namespace Core;

use Helpers\PhpMailer\Mail;

/**
 * Record and email/display errors or a custom error message.
 */
class Logger
{
    /**
    * Determins if error should be emailed to SITEEMAIL defined in app/Core/Config.php.
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
    public static $errorFile = 'Logs/error.log';

    /**
    * store errors for output.
    *
    * @var string
    */
    public static $error;

    /**
    * In the event of an error show this message.
    */
    public static function customErrorMsg()
    {
        if (self::$display) {
            echo '<pre>'.self::$error.'</pre>';
        } else {
            echo "<p>An error occurred, The error has been reported.</p>";
            exit;
        }

    }

    /**
    * Saved the exception and calls customer error function.
    *
    * @param  exeption $e
    */
    public static function exceptionHandler($e)
    {
        self::newMessage($e);
    }

    /**
    * Saves error message from exception.
    *
    * @param  numeric $number  error number
    * @param  string  $message the error
    * @param  string  $file    file originated from
    * @param  numeric $line    line number
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
    * @param  Exception $exception
    * @param  boolean   $printError show error or not
    * @param  boolean   $clear       clear the errorlog
    * @param  string    $errorFile  file to save to
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

        $logMessage = "Exception information:\n
           Date: {$date}\n
           Message: {$message}\n
           Code: {$code}\n
           File: {$file}\n
           Line: {$line}\n
           Stack trace:\n
           {$trace}\n
           ---------\n\n";

        if (is_file(APPDIR.self::$errorFile) === false) {
            file_put_contents(APPDIR.self::$errorFile, '');
        }

        if (self::$clear) {
            $f = fopen(APPDIR.self::$errorFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }
        }

        // Append
        file_put_contents(APPDIR.self::$errorFile, $logMessage, FILE_APPEND);

        self::$error = $logMessage;
        self::customErrorMsg();

        //send email
        self::sendEmail($logMessage);
    }

    /**
    * Custom error.
    *
    * @param  string  $error       the error
    * @param  boolean $printError display error
    * @param  string  $errorFile  file to save to
    */
    public static function errorMessage($error)
    {
        $date = date('Y-m-d G:iA');
        $logMessage = "$date - $error\n\n";

        if (is_file(APPDIR.self::$errorFile) === false) {
            file_put_contents(APPDIR.self::$errorFile, '');
        }

        if (self::$clear) {
            $f = fopen(APPDIR.self::$errorFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }

            $content = null;
        } else {
            // Append
            file_put_contents(APPDIR.self::$errorFile, $logMessage, FILE_APPEND);
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
            $mail = new Mail();
            $mail->setFrom(SITEEMAIL);
            $mail->addAddress(SITEEMAIL);
            $mail->subject('New error on '.SITETITLE);
            $mail->body($message);
            $mail->send();
        }
    }
}
