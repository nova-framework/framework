<?php

use Smvc\Core\Config;

/**
 * Email (PHPMailer) configuration
 */
Config::set('emailer', array(
    'mailer_charset'       => 'iso-8859-1',
    'mailer_from_name'     => 'SMVC Website',
    'mailer_from_email'    => 'smvc@localhost',
    'mailer_mailer'        => 'mail',           // Could be 'mail' => 'sendmail' or 'smtp'

    /** Only when using smtp as mailer: */
    'mailer_smtp_host'     => 'localhost',
    'mailer_smtp_port'     => 25,
    'mailer_smtp_secure'   => '',    // Options: '' => 'ssl' or 'tls'
    'mailer_smtp_auth'     => false, // Use SMTPAuth, (false or true)
    'mailer_smtp_user'     => '',    // Only when using SMTPAuth
    'mailer_smtp_pass'     => '',    // Only when using SMTPAuth
    'mailer_smtp_authtype' => ''     // Options are LOGIN (default), PLAIN, NTLM, CRAM-MD5. Blank when not use SMTPAuth.
));
