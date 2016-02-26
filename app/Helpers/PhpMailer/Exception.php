<?php
/**
 * PHPMailer exception handler.
 *
 * @date May 18 2015
 */
namespace Helpers\PhpMailer;

/**
 * Exceptions for PHPMailer.
 */
class Exception extends \Exception
{
    /**
     * Prettify error message output.
     *
     * @return string
     */
    public function errorMessage()
    {
        $errorMsg = '<strong>'.$this->getMessage()."</strong><br />\n";
        echo $errorMsg;
    }
}
