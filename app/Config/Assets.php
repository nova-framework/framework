<?php
/**
 * Assets Configuration
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */

return array(
    // The driver type used for serving the Asset Files.
    'driver' => 'default',     // Supported: "default" and "custom".

    // The name of Assets Dispatcher used as 'custom' driver.
    'dispatcher' => 'Shared\Assets\CustomDispatcher',

    // Wheter or not the CSS and JS files are automatically compressed.
    'compress' => true,

    // The Caching configuration.
    'cache' => array(
        // The local Assets Cache options.
        'active'   => true,
        'lifeTime' => 1440,
        'baseUri'  => 'cache',

        // The browser Cache Control options.
        'ttl'          => 600,
        'maxAge'       => 10800,
        'sharedMaxAge' => 600,
    ),

    // The Valid Vendor Paths - be aware that improper configuration of the Valid Vendor Paths could introduce
    // severe security issues, try to limit the access to a precise area, where aren't present "unsafe" files.
    //
    // '/vendor/almasaeed2010/adminlte/dist/css/AdminLTE.min.css'
    //          ^____________________^____^____________________This are the parts of path which are validated.
    //
    'paths' => array(
        'almasaeed2010/adminlte' => array(
            'bootstrap',
            'dist',
            'plugins'
        ),
        'twbs/bootstrap' => 'dist',
    ),
);
