<?php
/**
 * Mail Helper
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */

namespace Helpers;

use \PHPMailer;

/**
 * Custom class for PHPMailer to uniform sending emails.
 */
class Mailer extends PHPMailer
{
    /**
     * From
     *
     * @var string $From set sender email
     */
    public $From     = 'noreply@domain.com';

    /**
     * FromName
     *
     * @var string $FromName set sender name
     */
    public $FromName = SITETITLE;

    /**
     * Host
     *
     * @var string $Host set sender SMTP Route
     */
    //public $Host     = 'smtp.gmail.com';

    /**
     * Mailer
     *
     * @var string $Mailer set type default is SMTP
     */
    //public $Mailer   = 'smtp';

    /**
     * SMTPAuth
     *
     * @var string $SMTPAuth use authenticated
     */
    //public $SMTPAuth = true;

    /**
     * Username
     *
     * @var string $Username set username
     */
    //public $Username = 'email';

    /**
     * Password
     *
     * @var string $Password set password
     */
    //public $Password = 'password';

    /**
     * SMTPSecure
     *
     * @var string $SMTPSecure set Secure SMTP
     */
    //public $SMTPSecure = 'tls';

    /**
     * WordWrap
     * @var integer $WordWrap set word wrap
     */
    public $WordWrap = 75;

    /**
     * Subject
     *
     * @param  string $subject The subject of the email
     */
    public function subject($subject)
    {
        $this->Subject = $subject;
    }

    /**
     * Body
     *
     * @param  string $body The content of the email
     */
    public function body($body)
    {
        $this->Body = $body;
    }

    /**
     * Send
     *
     * @return none - sends the email.
     */
    public function send()
    {
        $this->AltBody = strip_tags(stripslashes($this->Body))."\n\n";
        $this->AltBody = str_replace("&nbsp;", "\n\n", $this->AltBody);

        return parent::send();
    }

    /**
     * Clean Message.
     * Remove email specific characters from a given string
     *
     * @param string. Email body.
     * @return string.
     */
    public function cleanMessage($string)
    {
        $bad = array("content-type", "bcc:", "to:", "cc:", "href");

        return str_replace($bad, "", $string);
    }
}
