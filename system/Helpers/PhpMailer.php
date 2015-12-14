<?php
/**
 * PhpMailer wrapper, just the default PHPMailer but extended so we can copy
 * our config into the PHPMailer class.
 *
 * @author Tom Valk <tomvalk@lt-box.info>
 * @date 14, December 2015
 *
 * @since 3.0
 */

namespace Smvc\Helpers;


/**
 * Class PhpMailer, extends \PHPMailer for adding in the Config.
 *
 * @package Helpers
 */
class PhpMailer extends \PHPMailer
{
    public function __construct()
    {
        parent::__construct();

        // Set all config in myself
        $this->CharSet = MAILER_CHARSET;
        $this->FromName = MAILER_FROM_NAME;
        $this->From = MAILER_FROM_EMAIL;
        $this->Mailer = MAILER_MAILER;

        if ($this->Mailer === 'smtp')
        {
            $this->Host = MAILER_SMTP_HOST;
            $this->Port = MAILER_SMTP_PORT;
            $this->SMTPSecure = MAILER_SMTP_SECURE;
            $this->SMTPAuth = MAILER_SMTP_AUTH;
            $this->Username = MAILER_SMTP_USER;
            $this->Password = MAILER_SMTP_PASS;
            $this->AuthType = MAILER_SMTP_AUTHTYPE;
        }
    }
}