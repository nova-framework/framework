<?php
/**
 * Active Modules
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Config\Config;


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

    'repository' => array(
        'demos' => array(
            'name'    => 'Demos',
            'enabled' => true,
            'order'   => 10001,
        ),
        'files' => array(
            'name'    => 'Files',
            'enabled' => true,
            'order'   => 9001,
        ),
        'system' => array(
            'name'    => 'System',
            'enabled' => true,
            'order'   => 8001,
        ),
        'users' => array(
            'name'    => 'Users',
            'enabled' => true,
            'order'   => 9001,
        ),
    ),
));
