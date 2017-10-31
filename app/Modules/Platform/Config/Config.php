<?php
/**
 * Config - the Module's specific Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


return array(
    'activityLimit' => 5,   // In minutes.

    'throttle' => array(
        'lockoutTime' => 1, // In minutes.
        'maxAttempts' => 5,
    ),

    'tokenLogin' => array(
        'validity' => 15,   // In minutes.
    ),

    'reminders' => array(
        'messages' => array(
            'password' => __d('platform', 'Passwords must be at least six characters and match the confirmation.'),
            'user'     => __d('platform', 'We can\'t find an User with that e-mail address.'),
            'token'    => __d('platform', 'This password reset token is invalid.'),
            'sent'     => __d('platform', 'Password reminder sent!'),
            'reset'    => __d('platform', 'Password has been reset!'),
        ),
    ),
);
