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
use Smvc\Core\Config;


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

        $config = Config::get('emailer');

        // Set all config in myself
        $this->CharSet = $config['mailer_charset'];
        $this->FromName = $config['mailer_from_name'];
        $this->From = $config['mailer_from_email'];
        $this->Mailer = $config['mailer_mailer'];

        if ($this->Mailer === 'smtp')
        {
            $this->Host = $config['mailer_smtp_host'];
            $this->Port = $config['mailer_smtp_port'];
            $this->SMTPSecure = $config['mailer_smtp_secure'];
            $this->SMTPAuth = $config['mailer_smtp_auth'];
            $this->Username = $config['mailer_smtp_user'];
            $this->Password = $config['mailer_smtp_pass'];
            $this->AuthType = $config['mailer_smtp_authtype'];
        }
    }
}