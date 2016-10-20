<?php
/**
 * Active Modules
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Nova\Config\Config;


Config::set('modules', array(
    //--------------------------------------------------------------------------
    // Path to Modules
    //--------------------------------------------------------------------------

    'path' => APPDIR .'Modules',

    //--------------------------------------------------------------------------
    // Modules Base Namespace
    //--------------------------------------------------------------------------

    'namespace' => 'App\Modules\\',

    //--------------------------------------------------------------------------
    // Registered Modules
    //--------------------------------------------------------------------------

    'modules' => array(
        'demos' => array(
            'namespace' => 'Demos',
            'enabled'   => true,
            'order'     => 10001,
        ),
        'files' => array(
            'namespace' => 'Files',
            'enabled'   => true,
            'order'     => 9001,
        ),
        'system' => array(
            'namespace' => 'System',
            'enabled'   => true,
            'order'     => 8001,
        ),
        'users' => array(
            'namespace' => 'Users',
            'enabled'   => true,
            'order'     => 9001,
        ),
    ),
));
