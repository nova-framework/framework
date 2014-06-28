<?php namespace helpers\phpmailer;
/**
 * PHPMailer exception handler
 * @package PHPMailer
 */
class phpmailerException extends \Exception
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