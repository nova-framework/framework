<?php
/**
 * Routing - the Routing Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


return array(
    /*
     * The Asset Files Serving configuration.
     */
    'assets' => array(
        // The driver type used for serving the Asset Files.
        'driver' => 'default',                                  // Supported: "default", "custom".

        // The Assets Dispatcher used while the driver is on 'custom' mode.
        'dispatcher' => 'Shared\Routing\Assets\CustomDispatcher',

        // Wheter or not the CSS and JS files are automatically compressed.
        'compress' => true,

        // The browser Cache Control options.
        'cache' => array(
            'ttl'          => 600,
            'maxAge'       => 10800,
            'sharedMaxAge' => 600,
        ),

        // The Valid Vendor Paths - be aware that improper configuration of the Valid Vendor Paths could introduce
        // severe security issues, try to limit the access to a precise area, where aren't present "unsafe" files.
        //
        // '/vendor/almasaeed2010/adminlte/dist/css/AdminLTE.min.css'
        //          ^____________________^____^____________________Those are the parts of path which are validated.
        //
        'paths' => array(
            'almasaeed2010/adminlte' => array(
                'bootstrap',
                'dist',
                'plugins'
            ),
            'twbs/bootstrap' => 'dist',
        ),
    ),
);
