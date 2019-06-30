<?php
/**
 * Config - the Module's specific Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


return array(
    'throttle' => array(
        'lockoutTime' => 1, // In minutes.
        'maxAttempts' => 5,
    ),

    'tokens' => array(
        'verify' => array(
            'validity' => 60, // In minutes.
        ),

        'login' => array(
            'validity' => 15, // In minutes.
        ),
    ),

    'reminders' => array(
        'messages' => array(
            'password' => __d('users', 'Passwords must be at least six characters and match the confirmation.'),
            'user'     => __d('users', 'We can\'t find an User with that e-mail address.'),
            'token'    => __d('users', 'This password reset token is invalid.'),
            'sent'     => __d('users', 'Password reminder sent!'),
            'reset'    => __d('users', 'Password has been reset!'),
        ),
    ),
);
