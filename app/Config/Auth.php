<?php
/**
 * Auth configuration
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


return array(
    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => array(
        'guard'    => 'web',
        'reminder' => 'users',
    ),

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

                // The paths where the intended redirects can't be used.
                'nonintend' => array(
                    'logout',
                ),
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
            'model'  => 'App\Models\User',
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Here you may set the options for resetting passwords including the view
    | that is your password reset e-mail. You may also set the name of the
    | table that maintains all of the reset tokens for your application.
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'reminders' => array(
        'users' => array(
            'provider' => 'users',
            'email'    => 'Emails/Auth/Reminder',
            'table'    => 'password_reminders',
            'expire'   => 60,
        ),
    ),
);
