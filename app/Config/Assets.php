<?php
/**
 * Assets Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */

return array(

    /*
    |--------------------------------------------------------------------------
    | Assets Driver
    |--------------------------------------------------------------------------
    |
    | The driver type used for serving the Asset Files.
    |
    | Supported: "default" and "custom".
    |
    | Default: 'default'
    |
    */

    'driver' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Assets Driver
    |--------------------------------------------------------------------------
    |
    | The name of Assets Dispatcher used as 'custom' driver.
    |
    | Default: 'Shared\Assets\CustomDispatcher'
    |
    */

    'dispatcher' => 'Shared\Assets\CustomDispatcher',

    /*
    |--------------------------------------------------------------------------
    | Compress Assets
    |--------------------------------------------------------------------------
    |
    | Whether or not the CSS and JS files are automatically compressed.
    |
    | Default: true
    |
    */

    'compress' => true,

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | The Caching configuration.
    |
    */

    'cache' => array(
        /*
        |----------------------------------------------------------------------
        | Local Assets
        |----------------------------------------------------------------------
        |
        */

        'active'   => true,
        'lifeTime' => 1440,
        'baseUri'  => 'cache',

        /*
        |----------------------------------------------------------------------
        | Browser Cache Control
        |----------------------------------------------------------------------
        |
        */

        'ttl'          => 600,
        'maxAge'       => 10800,
        'sharedMaxAge' => 600,
    ),

    /*
    |--------------------------------------------------------------------------
    | Vendor Paths
    |--------------------------------------------------------------------------
    |
    | The Valid Vendor Paths - be aware that improper configuration of the
    | Valid Vendor Paths could introduce severe security issues, try to limit
    | the access to a precise area, where there aren't any "unsafe" files
    | present.
    |
    | '/vendor/almasaeed2010/adminlte/dist/css/AdminLTE.min.css'
    |               ^___________^______^____________________
    |
    | These are parts of the path which are validated.
    |
    */

    'paths' => array(
        'almasaeed2010/adminlte' => array(
            'bootstrap',
            'dist',
            'plugins'
        ),
        'twbs/bootstrap' => 'dist',
    ),
);
