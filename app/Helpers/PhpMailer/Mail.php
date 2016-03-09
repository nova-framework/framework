<?php
/**
 * Mail Helper.
 *
 * @author David Carr - dave@simplemvcframework.com
 *
 * @version 1.0
 * @date May 18 2015
 * @date updated Sept 19, 2015
 */
namespace Helpers\PhpMailer;

/**
 * Custom class for PHPMailer to uniform sending emails.
 */
class Mail extends PhpMailer
{
    /**
     * From.
     *
     * @var string set sender email
     */
    public $From = 'noreply@domain.com';

    /**
     * FromName.
     *
     * @var string set sender name
     */
    public $FromName = SITETITLE;

    /**
     * Host.
     *
     * @var string set sender SMTP Route
     */
    //public $Host     = 'smtp.gmail.com';

    /**
     * Mailer.
     *
     * @var string set type default is SMTP
     */
    //public $Mailer   = 'smtp';

    /**
     * SMTPAuth.
     *
     * @var string use authenticated
     */
    //public $SMTPAuth = true;

    /**
     * Username.
     *
     * @var string set username
     */
    //public $Username = 'email';

    /**
     * Password.
     *
     * @var string set password
     */
    //public $Password = 'password';

    /**
     * SMTPSecure.
     *
     * @var string set Secure SMTP
     */
    //public $SMTPSecure = 'tls';

    /**
     * WordWrap.
     *
     * @var int set word wrap
     */
    public $WordWrap = 75;

    /**
     * Subject.
     *
     * @param string $subject The subject of the email
     */
    public function subject($subject)
    {
        $this->Subject = $subject;
    }

    /**
     * Body.
     *
     * @param string $body The content of the email
     */
    public function body($body)
    {
        $this->Body = $body;
    }

    /**
     * Send.
     *
     * @return none - sends the email.
     */
    public function send()
    {
        $this->AltBody = strip_tags(stripslashes($this->Body))."\n\n";
        $this->AltBody = str_replace('&nbsp;', "\n\n", $this->AltBody);

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
        $bad = array("content-type","bcc:","to:","cc:","href");
        return str_replace($bad,"",$string);
    }
}
