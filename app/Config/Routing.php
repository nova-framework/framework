<?php


return array(
    /*
     * The registered Routes sorting configuration.
     */
    'sorting' => false,

    /*
     * The Asset Files Serving configuration.
     */
    'assets' => array(
        // The path to the asset files.
        'path' => BASEPATH .'assets',

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

            // AdminLTE
            'almasaeed2010/adminlte' => array(
                'bower_components',
                'dist',
                'plugins'
            ),

            // Bootstrap
            'twbs/bootstrap' => 'dist',
        ),
    ),

    /*
     * The Protected Files Serving configuration.
     */
    'files' => array(
        // The path to the protected files.
        'path' => BASEPATH .'files',

        // The access token validity - in minutes.
        'validity' => 180,
    ),
);
