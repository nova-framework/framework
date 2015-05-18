<?php
namespace Helpers\PhpMailer;

/**
 * PHPMailer exception handler
 * @package PHPMailer
 * @date May 18 2015
 */
class PhpMailerException extends \Exception
{
    /**
     * Prettify error message output
     * @return string
     */
    public function errorMessage()
    {
        $errorMsg = '<strong>' . $this->getMessage() . "</strong><br />\n";
        echo $errorMsg;
    }
}
