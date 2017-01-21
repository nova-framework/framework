<?php
/**
 * Services Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */

return array(

   /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => array(
        'domain' => '',
        'secret' => '',
    ),

    'mandrill' => array(
        'secret' => '',
    ),

    'ses' => array(
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
    ),

    'stripe' => array(
        'model'  => 'User',
        'secret' => '',
    ),
);
