<?php
/**
 * Auth configuration
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


return array(
    /*
    |--------------------------------------------------------------------------
    | Default Authentication Guard
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard". You may change
    | this default as required, but it's a perfect start for most applications.
    |
    */

    'default' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the extended user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */

    'guards' => array(
        'web' => array(
            'driver'   => 'session',
            'provider' => 'users',

            'paths' => array(
                'authorize' => 'login',
                'dashboard' => 'admin/dashboard',
            ),
        ),
        'api' => array(
            'driver'   => 'token',
            'provider' => 'users',
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "extended"
    |
    */

    'providers' => array(
        'users' => array(
            'driver' => 'extended',
            'model'  => 'AcmeCorp\Backend\Models\User',
        ),
    ),

);
