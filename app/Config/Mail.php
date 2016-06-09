<?php
/**
 * Mailer Configuration
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Core\Config;


Config::set('mail', array(
    'driver' => 'smtp',
    'host'   => '',
    'port'   => 587,
    'from'   => array(
        'address' => 'admin@novaframework.dev',
        'name'    => SITETITLE,
    ),
    'encryption' => 'tls',
    'username'   => '',
    'password'   => '',
    'sendmail'   => '/usr/sbin/sendmail -bs',

    // Whether or not the Mailer will pretend to send the messages.
    'pretend' => true,
));
