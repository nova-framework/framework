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

namespace Nova\Helpers;

use Nova\Config;


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

        // Get the Mailer configuration.
        $config = Config::get('emailer');

        // Set all Config options to phpMailer engine.
        $this->CharSet  = $config['charset'];
        $this->FromName = $config['from_name'];
        $this->From     = $config['from_email'];
        $this->Mailer   = $config['mailer'];

        if ($this->Mailer !== 'smtp') {
            // Let's make Tom happy!
            return null;
        }

        // SMTP only options.
        $this->Host       = $config['smtp_host'];
        $this->Port       = $config['smtp_port'];
        $this->SMTPSecure = $config['smtp_secure'];
        $this->SMTPAuth   = $config['smtp_auth'];
        $this->Username   = $config['smtp_user'];
        $this->Password   = $config['smtp_pass'];
        $this->AuthType   = $config['smtp_authtype'];
    }
}
